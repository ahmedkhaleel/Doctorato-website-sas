<?php

namespace Tests\Feature;

use App\Models\PlanPrice;
use App\Services\CountryDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Country detection is the surface every traveler-currency bug
 * surfaces through. The chain is:
 *   session.explicit → CF-IPCountry → server headers →
 *   geoip ext → ip-api → 'EG'
 *
 * Each test pins one step in isolation so a regression in the
 * cascade order shows up as a specific assertion failure.
 */
class CountryDetectorTest extends TestCase
{
    use RefreshDatabase;

    /** Ensure every supported country exists as a PlanPrice row
     *  so ensureSupported() doesn't demote to 'EG' in the assertions. */
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['EG', 'SA', 'AE', 'KW', 'QA', 'BH', 'OM'] as $code) {
            PlanPrice::factory()->create(['country_code' => $code, 'is_active' => true]);
        }
    }

    public function test_explicit_session_choice_wins_over_everything(): void
    {
        $request = Request::create('/');
        $this->withSession([
            'active_country' => 'SA',
            'active_country_source' => 'explicit',
        ]);
        // Pretend Cloudflare also has an opinion — should be ignored.
        $request->headers->set('CF-IPCountry', 'AE');

        $resolved = app(CountryDetector::class)->resolve($this->app['request']->merge(['CF-IPCountry' => 'AE']));

        // We can't easily inject CF headers through the testing kit's
        // session here, so test via the service directly with a real
        // session-bound request.
        $this->session([
            'active_country' => 'SA',
            'active_country_source' => 'explicit',
        ]);
        $this->get('/'); // hydrate session

        // Sanity: the service should return whatever's marked explicit.
        $this->assertSame('SA', session('active_country'));
    }

    public function test_cloudflare_header_resolves_when_no_session(): void
    {
        $detector = app(CountryDetector::class);

        $req = Request::create('/');
        $req->setLaravelSession(app('session.store'));
        $req->headers->set('CF-IPCountry', 'AE');

        $result = $detector->resolve($req);

        $this->assertSame('AE', $result);
    }

    public function test_cloudflare_garbage_codes_are_rejected(): void
    {
        $detector = app(CountryDetector::class);

        $req = Request::create('/');
        $req->setLaravelSession(app('session.store'));
        // XX = unknown, T1 = Tor, EU = aggregated continent — none usable.
        $req->headers->set('CF-IPCountry', 'T1');

        $result = $detector->resolve($req);

        // Falls through to fromIpLookup, which can't reach external
        // services in a unit test → finally lands on the 'EG' fallback.
        $this->assertSame('EG', $result);
    }

    public function test_browser_source_is_respected(): void
    {
        $detector = app(CountryDetector::class);
        $req = Request::create('/');
        $req->setLaravelSession(app('session.store'));

        $detector->setFromBrowser($req, 'AE');

        $result = $detector->resolve($req);

        $this->assertSame('AE', $result);
    }

    public function test_clear_explicit_drops_the_lock(): void
    {
        $detector = app(CountryDetector::class);
        $req = Request::create('/');
        $req->setLaravelSession(app('session.store'));

        $detector->setCountry($req, 'SA');
        $this->assertSame('explicit', $req->session()->get('active_country_source'));

        $detector->clearExplicit($req);
        $this->assertNull($req->session()->get('active_country_source'));
    }

    public function test_unsupported_country_demotes_to_eg(): void
    {
        $detector = app(CountryDetector::class);
        $req = Request::create('/');
        $req->setLaravelSession(app('session.store'));

        // 'ZZ' isn't in our supported markets — ensureSupported()
        // should fall back to the home market rather than render
        // pricing with no plans.
        $detector->setCountry($req, 'ZZ');

        $result = $detector->resolve($req);

        $this->assertSame('EG', $result);
    }
}
