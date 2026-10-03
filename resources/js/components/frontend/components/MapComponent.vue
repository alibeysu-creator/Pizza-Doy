<template>
    <div class="w-full max-w-[630px] rounded-2xl bg-white">
        <LoadingContentComponent :props="loading" />

        <div v-if="setting.autocomplete || setting.currentLocation" class="mb-4">
            <div v-if="setting.autocomplete"
                class="w-full h-12 shadow-xs rounded-lg flex items-center gap-3 pl-[18px] pr-3 bg-white border border-[#D9DBE9]">
                <i class="lab lab-search-normal lab-font-size-24"></i>
                <input id="map-autocomplete-input" type="text"
                    placeholder="Straße, Hausnummer, PLZ oder Ort eingeben"
                    autocomplete="street-address"
                    @keydown.enter.prevent
                    class="w-full h-full placeholder:text-sm placeholder:font-normal placeholder:font-public text-base font-public outline-none">
            </div>

            <button v-if="setting.currentLocation" id="map-current-location" type="button"
                class="mt-3 w-full h-11 rounded-lg flex items-center justify-center gap-2 border border-primary text-primary bg-white font-medium">
                <i class="lab lab-gps-tracker"></i>
                <span>Meinen Standort verwenden</span>
            </button>
        </div>

        <div ref="theGoogleMap" id="the-google-map" class="w-full h-[180px] rounded-xl mb-4"></div>
    </div>
</template>

<script>
import { Loader } from "google-maps";
import LoadingContentComponent from "./LoadingContentComponent";
import _ from "lodash";
import ENV from '../../../config/env';

const options = { libraries: ["places", "geometry", "drawing"] };
const loader = new Loader(ENV.GOOGLE_MAP_KEY, options);

export default {
    name: "MapComponent",
    components: { LoadingContentComponent },
    props: {
        location: Object,
        position: Function,
        setting: {
            type: Object,
            default: () => ({
                autocomplete: true,
                mouseEvent: true,
                currentLocation: true,
            })
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            currentLocation: {
                lat: null,
                lng: null,
            },
            address: null,
        }
    },
    mounted: async function () {
        this.loading.isActive = true;

        const hasSavedLocation = this.location
            && this.location.lat !== null
            && this.location.lat !== ""
            && this.location.lng !== null
            && this.location.lng !== "";

        if (hasSavedLocation) {
            this.currentLocation = {
                lat: parseFloat(this.location.lat),
                lng: parseFloat(this.location.lng),
            };
            await this.mainMap(this.currentLocation, true);
        } else {
            // Karte anzeigen, aber NICHT automatisch nach GPS fragen.
            // Der Kunde kann die Adresse eintippen oder den Standort-Button drücken.
            await this.mainMap({ lat: 51.1657, lng: 10.4515 }, false);
        }
    },
    methods: {
        mainMap: async function (location, hasInitialPosition = false) {
            try {
                const google = await loader.load();

                const map = new google.maps.Map(this.$refs.theGoogleMap, {
                    center: location,
                    zoom: hasInitialPosition ? 15 : 6,
                });

                let markers = [];

                const setMarker = (latLng) => {
                    for (let i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }
                    markers = [];

                    const marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                    });
                    markers.push(marker);
                    map.setZoom(15);
                    map.setCenter(marker.getPosition());
                };

                if (hasInitialPosition) {
                    setMarker(location);
                }

                if (this.setting.currentLocation) {
                    const currentLocationButton = document.getElementById('map-current-location');
                    if (currentLocationButton) {
                        currentLocationButton.addEventListener("click", () => {
                            if (!navigator.geolocation) {
                                alert("Ihr Browser unterstützt die Standortbestimmung nicht. Bitte geben Sie Ihre Adresse manuell ein.");
                                return;
                            }

                            this.loading.isActive = true;
                            navigator.geolocation.getCurrentPosition(
                                async (position) => {
                                    const latLng = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude,
                                    };

                                    this.currentLocation = latLng;
                                    setMarker(latLng);
                                    await this.setPosition();
                                    this.loading.isActive = false;
                                },
                                () => {
                                    this.loading.isActive = false;
                                    alert("Der Standort konnte nicht ermittelt werden. Bitte geben Sie Ihre Adresse manuell ein.");
                                },
                                {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 60000,
                                }
                            );
                        });
                    }
                }

                if (this.setting.mouseEvent) {
                    map.addListener("click", async (mapsMouseEvent) => {
                        const latLng = mapsMouseEvent.latLng.toJSON();
                        this.currentLocation = latLng;
                        setMarker(latLng);
                        await this.setPosition();
                    });
                }

                if (this.setting.autocomplete) {
                    const input = document.getElementById('map-autocomplete-input');
                    if (input) {
                        const autocomplete = new google.maps.places.Autocomplete(input, {
                            fields: ["geometry", "formatted_address", "address_components", "name"],
                            types: ["address"],
                        });

                        autocomplete.addListener('place_changed', async () => {
                            const place = autocomplete.getPlace();

                            if (!place.geometry || !place.geometry.location) {
                                alert("Bitte wählen Sie eine Adresse aus den Google-Vorschlägen aus.");
                                return;
                            }

                            const latLng = {
                                lat: place.geometry.location.lat(),
                                lng: place.geometry.location.lng(),
                            };

                            this.currentLocation = latLng;
                            setMarker(latLng);
                            await this.setPosition();
                        });
                    }
                }

                if (hasInitialPosition) {
                    await this.setPosition();
                }
            } catch (error) {
                console.error("Google Maps konnte nicht geladen werden:", error);
                alert("Die Karte konnte nicht geladen werden. Bitte versuchen Sie es erneut.");
            } finally {
                this.loading.isActive = false;
            }
        },

        setPosition: async function () {
            if (this.currentLocation.lat === null || this.currentLocation.lng === null) {
                return;
            }

            let other = {
                "rodeNo": null,
                "block": null,
                "area": null,
                "city": null,
                "zipCode": null,
                "state": null,
                "country": null,
            };

            try {
                const google = await loader.load();
                const latLngLiteral = new google.maps.LatLng(this.currentLocation.lat, this.currentLocation.lng);
                const geocoder = new google.maps.Geocoder();
                const res = await geocoder.geocode({ location: latLngLiteral });

                for (let i = 0; i < res.results.length; i++) {
                    for (let j = 0; j < res.results[i].address_components.length; j++) {
                        const component = res.results[i].address_components[j];
                        const types = component.types || [];

                        if (types.includes("route") && other.rodeNo === null) {
                            other.rodeNo = component.long_name;
                        }

                        if (types.includes("neighborhood") && other.block === null) {
                            other.block = component.long_name;
                        }

                        if (types.includes("sublocality_level_1") && other.area === null) {
                            other.area = component.long_name;
                        }

                        if (types.includes("locality") && other.city === null) {
                            other.city = component.long_name;
                        }

                        if (types.includes("postal_code") && other.zipCode === null) {
                            other.zipCode = component.long_name;
                        }

                        if (types.includes("administrative_area_level_1") && other.state === null) {
                            other.state = component.long_name;
                        }

                        if (types.includes("country") && other.country === null) {
                            other.country = component.long_name;
                        }
                    }
                }

                let formatted_address = "";
                if (res.results.length > 0 && res.results[0].formatted_address) {
                    formatted_address = res.results[0].formatted_address;
                } else {
                    _.forEach(other, (value, index) => {
                        if (value !== null && value !== "") {
                            formatted_address += value;
                            if (index !== "country") {
                                formatted_address += ",";
                            }
                            formatted_address += " ";
                        }
                    });
                }

                this.address = formatted_address.trim();
                this.position({ address: this.address, other: other, location: this.currentLocation });
            } catch (error) {
                console.error("Adresse konnte nicht ermittelt werden:", error);
                this.position({ address: this.address, other: other, location: this.currentLocation });
            }
        },
    }
}
</script>
