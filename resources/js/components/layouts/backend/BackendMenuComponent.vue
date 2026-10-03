<template>
    <aside class="db-sidebar"
        :class="$route.path.includes('kitchen-display-system') || $route.path.includes('order-status-screen') ? 'hidden' : ''">
        <div class="db-sidebar-header">
            <router-link class="w-24" :to="{ name: 'frontend.home' }">
                <img :src="setting.theme_logo" alt="logo">
            </router-link>
            <button @click.prevent="handleSidebar" class="fa-solid fa-xmark xmark-btn close-db-menu"></button>
        </div>
        <div class="p-4 flex flex-col items-start gap-y-2">
            <h3 class="capitalize text-sm font-medium text-heading">{{ $t('label.restaurant_status') }}</h3>
            <label for="restaurantStatus" class="inline-flex relative items-center gap-3 cursor-pointer">
                <span class="text-xs font-medium text-heading">{{ $t('label.closed') }}</span>
                <input type="checkbox" v-model="info.is_restaurant_open" id="restaurantStatus" class="sr-only peer"
                    @change="changeRestaurantStatus">
                <div
                    class="w-11 h-6 bg-gray-200 relative peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                </div>
                <span class="text-xs font-medium text-heading">{{ $t('label.open') }}</span>
            </label>
        </div>
        <!--        {{ menus }}-->
        <nav class="db-sidebar-nav">
            <ul class="db-sidebar-nav-list" v-if="menus.length > 0" v-for="menu in menus" :key="menu">
                <li class="db-sidebar-nav-item" v-if="menu.url === '#'" @click.prevent="sidebarActive($event)">
                    <a href="javascript:void(0);" class="db-sidebar-nav-title">
                        {{ $t('menu.' + menu.language) }}
                    </a>
                </li>

                <li class="db-sidebar-nav-item" v-else @click.prevent="sidebarActive($event)">
                    <router-link :to="'/admin/' + menu.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="menu.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + menu.language) }}</span>
                    </router-link>
                </li>

                <li class="db-sidebar-nav-item" v-if="menu.children" v-for="children in menu.children"
                    @click.prevent="sidebarActive($event)">
                    <router-link :to="'/admin/' + children.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="children.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + children.language) }}</span>
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
</template>

<script>
import alertService from "../../../services/alertService";
import activityEnum from "../../../enums/modules/activityEnum";

export default {
    name: "BackendMenuComponent",
    data: function () {
        return {
            activeChildId: 0,
            sidebarOpen: false,
            info: {
                is_restaurant_open: false
            }
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        menus: function () {
            return this.$store.getters.authMenu;
        },
        sidebar() {
            return this.$store.getters['globalState/lists'].topSidebar;
        },
    },
    mounted() {
        this.defaultSidebarActive();

    },
    methods: {
        sidebarActive: function (e) {
            const activeMenu = document.querySelector('.db-sidebar-nav-item.active');
            if (activeMenu) {
                activeMenu.classList.remove('active');
            }
            e?.currentTarget?.classList?.add('active');
        },
        defaultSidebarActive: function () {
            if (document?.querySelector(".db-sidebar-nav-menu")?.classList?.contains("active")) {
                document?.querySelector('.db-sidebar-nav-menu')?.parentElement?.classList?.add('active');
            } else {
                document?.querySelector('.router-link-exact-active')?.parentElement?.classList?.add('active');
            }
        },
        handleSidebar: function () {
            this.sidebarOpen = !this.sidebar;
            this.$store.dispatch("globalState/set", { topSidebar: this.sidebarOpen });

            if (document?.querySelector(".db-sidebar")?.classList?.contains("active")) {
                document?.querySelector(".db-main")?.classList?.remove("expand");
                document?.querySelector(".db-sidebar")?.classList?.remove("active");
            } else {
                document?.querySelector(".db-sidebar")?.classList?.add("active");
                document?.querySelector(".db-main")?.classList?.add("expand");
            }
        },
        changeRestaurantStatus: function () {
            const isOpening = this.info.is_restaurant_open;
            this.$store.dispatch('site/restaurantStatus', {
                site_restaurant_status: isOpening ? activityEnum.ENABLE : activityEnum.DISABLE
            }).then(res => {
                this.$store.dispatch('frontendSetting/lists');
                if (isOpening) {
                    alertService.success(this.$t('message.restaurant_opened'));
                } else {
                    alertService.success(this.$t('message.restaurant_close'));
                }
            }).catch(err => {
                alertService.error(err.response.data.message);
            });
        },
    },
    watch: {
        setting: {
            deep: true,
            handler(newSetting) {
                if (newSetting.site_restaurant_status) {
                    this.info.is_restaurant_open = newSetting.site_restaurant_status == activityEnum.ENABLE;
                }
            },
            immediate: true
        }
    }
}
</script>