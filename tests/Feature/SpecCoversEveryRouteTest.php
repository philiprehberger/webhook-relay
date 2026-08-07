<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

/**
 * Every registered `/v1/` route must appear in the OpenAPI spec.
 *
 * The existing drift job compares the spec against the generated SDKs, which
 * cannot detect a route that never entered the spec in the first place — the
 * check has nothing to compare. `POST /v1/sandbox/keys`, the unauthenticated
 * key-minting endpoint and the most security-relevant route in the service,
 * was missing from the contract for exactly that reason.
 */
class SpecCoversEveryRouteTest extends TestCase
{
    public function test_every_registered_v1_route_is_documented(): void
    {
        $spec = Yaml::parseFile(base_path('openapi/spec.yaml'));
        $documented = [];

        foreach ($spec['paths'] ?? [] as $path => $operations) {
            foreach (array_keys($operations) as $method) {
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'], true)) {
                    $documented[] = strtoupper($method).' '.$path;
                }
            }
        }

        $missing = [];

        foreach (Route::getRoutes() as $route) {
            $uri = '/'.ltrim($route->uri(), '/');

            if (! str_starts_with($uri, '/v1/')) {
                continue;
            }

            foreach ($route->methods() as $method) {
                if (in_array($method, ['HEAD', 'OPTIONS'], true)) {
                    continue;
                }

                // Laravel writes {id}; the spec may name the parameter
                // differently, so compare on the path shape.
                $normalised = preg_replace('/\{[^}]+\}/', '{}', $uri);

                $found = false;
                foreach ($documented as $entry) {
                    [$docMethod, $docPath] = explode(' ', $entry, 2);
                    if ($docMethod === $method && preg_replace('/\{[^}]+\}/', '{}', $docPath) === $normalised) {
                        $found = true;
                        break;
                    }
                }

                if (! $found) {
                    $missing[] = "{$method} {$uri}";
                }
            }
        }

        $this->assertSame(
            [],
            $missing,
            "These routes are registered but absent from openapi/spec.yaml:\n  ".implode("\n  ", $missing)
                ."\n\nThe spec is the published contract. A route that is not in it is invisible to "
                ."the SDK generator, the Scalar reference, and anyone reading the contract instead "
                ."of the routes file."
        );
    }
}
