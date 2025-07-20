<?php

namespace Database\Seeders;

use App\Services\GeoCountryService;
use App\Services\GeoLocalGovService;
use App\Services\GeoStateService;
use Illuminate\Database\Seeder;

class GeoDataSeeder extends Seeder
{
    public function __construct(
        private readonly GeoCountryService  $geoCountryService,
        private readonly GeoStateService    $geoStateService,
        private readonly GeoLocalGovService $geoLocalGovService,
    )
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = file_get_contents(database_path('raw/countries.json'));
        $countries = json_decode($countries, true);

        foreach ($countries as $country) {
            echo " - [{$country['name']}] creating...\n";

            $createdCountry = $this->geoCountryService->create(
                name: $country['name'],
                code: $country['code2'],
                capital: $country['capital'],
                region: $country['region'],
                subregion: $country['subregion']
            );

            echo "   - [{$country['name']}] seeding states...\n";
            foreach ($country['states'] as $state) {
                $createdState = $this->geoStateService->create(
                    countryId: $createdCountry['id'],
                    name: $state['name'],
                    code: $state['code']
                );

                echo "     - [{$country['name']}][{$createdState['name']}] seeding local govs...\n";
                $subdivisions = $state['subdivision'] ?? [];
                $subdivisions = is_string($subdivisions) ? [] : $subdivisions;

                foreach ($subdivisions as $lgaName) {
                    $this->geoLocalGovService->create(
                        countryId: $createdCountry['id'],
                        stateId: $createdState['id'],
                        name: $lgaName,
                        code: null
                    );
                }
            }
        }
    }
}
