<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 mb-4 sm:mb-0">
            <button type="button" @click="handleTab($event, '#information', '.db-tabBtn', '.db-tabDiv', 'active')"
                class="db-tabBtn !justify-start active">
                <i class="lab lab-information lab-font-size-16"></i>
                {{ $t('label.information') }}
            </button>
            <button type="button" class="db-tabBtn !justify-start"
                @click="handleTab($event, '#extra', '.db-tabBtn', '.db-tabDiv', 'active')"><i
                    class="lab lab-extra lab-font-size-16"></i>
                {{ $t('label.extras') }}
            </button>
        </div>
        
        <div class="db-tabDiv active" id="information">
            <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.item_categories') }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12 sm:col-5">
                    <img class="db-image" alt="category" :src="itemCategory.cover">
                </div>
                <div class="col-12 sm:col-7 md:pl-8">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ itemCategory.name }}</h3>
                    <label class="db-badge mb-3" :class="statusClass(itemCategory.status)">
                        {{ enums.statusEnumArray[itemCategory.status] }}
                    </label>
                    <p class="db-light-text">
                        {{ itemCategory.description }}
                    </p>
                </div>
            </div>
            </div>
        </div>
        </div>
        
        <div class="db-tabDiv" id="extra">
            <ItemExtraListComponent :item="parseInt($route.params.id)" />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import ItemExtraListComponent from "./extra/ItemExtraListComponent";

export default {
    name: "ItemCategoryShowComponent",
    components: {
        LoadingComponent,
        ItemExtraListComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            }
        }
    },
    computed: {
        itemCategory: function () {
            return this.$store.getters['itemCategory/show'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('itemCategory/show', this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        handleTab: function (event, targetID, targetButton, targetDiv, activeClass) {
            return appService.handleTab(event, targetID, targetButton, targetDiv, activeClass);
        }
    }
}
</script>
