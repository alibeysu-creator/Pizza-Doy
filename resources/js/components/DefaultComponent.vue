<template>

  <div :dir="direction">
    <div v-if="maintenance == 5">
      <FrontendNavbarComponent />
       <div
  style="
    display: flex;
    justify-content: center;
     background: #fff;
  "
>
  <div
    style="
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      text-align: center;
      max-width: 500px;
    "
  >
    <p>{{ maintenance_message }}</p>
  </div>
</div>

 
      <router-view v-if="allowDuringMaintenance"></router-view>







   

    </div>

    <div v-else-if="theme === 'frontend'">
      <FrontendNavbarComponent />
      <FrontendCartComponent />
      <router-view></router-view>
      <FrontendMobileNavBarComponent />
      <FrontendMobileAccountComponent />
      <FrontendCookiesComponent />
      <FrontendFooterComponent />
    </div>

    <div v-if="theme === 'backend'">
      <main class="db-main" v-if="logged">
        <BackendNavbarComponent />
        <BackendMenuComponent />
        <router-view></router-view>
      </main>

      <div v-if="!logged">
        <router-view></router-view>
      </div>
    </div>

    <div v-if="theme === 'table'">
      <TableNavbarComponent />
      <TableCartComponent />
      <router-view></router-view>
      <TableFooterComponent />
    </div>
  </div>
</template>

<script>
import BackendNavbarComponent from "./layouts/backend/BackendNavbarComponent";
import BackendMenuComponent from "./layouts/backend/BackendMenuComponent";
import FrontendNavbarComponent from "./layouts/frontend/FrontendNavBarComponent";
import FrontendFooterComponent from "./layouts/frontend/FrontendFooterComponent";
import FrontendMobileNavBarComponent from "./layouts/frontend/FrontendMobileNavBarComponent";
import FrontendMobileAccountComponent from "./layouts/frontend/FrontendMobileAccountComponent";
import FrontendCartComponent from "./layouts/frontend/FrontendCartComponent";
import FrontendCookiesComponent from "./layouts/frontend/FrontendCookiesComponent";
import TableNavbarComponent from "./layouts/table/TableNavBarComponent.vue";
import TableFooterComponent from "./layouts/table/TableFooterComponent.vue";
import TableCartComponent from "./layouts/table/TableCartComponent.vue";
import displayModeEnum from "../enums/modules/displayModeEnum";
import env from "../config/env";

export default {
  name: "DefaultComponent",
  components: {
    TableCartComponent,
    TableFooterComponent,
    TableNavbarComponent,
    FrontendCartComponent,
    FrontendMobileAccountComponent,
    FrontendMobileNavBarComponent,
    FrontendCookiesComponent,
    FrontendFooterComponent,
    FrontendNavbarComponent,
    BackendNavbarComponent,
    BackendMenuComponent,
  },
  data() {
    return {
      theme: "frontend",
    };
  },
  props: {
    maintenance: {
      type: Number,
      default: 10
    },
    "maintenance_message": {
      type: String,
      default: "This site is currently undergoing maintenance. Please check back later."
    }
  },


  computed: {
    direction: function () {
      return this.$store.getters['frontendLanguage/show'].display_mode === displayModeEnum.RTL ? 'rtl' : 'ltr';
    },
    logged: function () {
      return this.$store.getters.authStatus;
    },
    allowDuringMaintenance() {
      const allowedNames = ['frontend.login', 'login'];
      const allowedPaths = ['/login', '/auth/login'];

       if (this.theme === 'backend') {
        allowedNames.push('admin.dashboard');
        allowedPaths.push('/admin/dashboard');


        }

      const name = this.$route?.name;
      const path = this.$route?.path;

      return (name && allowedNames.includes(name)) ||
             (path && allowedPaths.includes(path));
    },


  },
  beforeMount() {
    this.$store
      .dispatch("frontendSetting/lists")
      .then((res) => {
        this.$store.dispatch("globalState/init", {
          branch_id: res.data.data.site_default_branch,
          language_id: res.data.data.site_default_language,
        });
      })
      .catch();


    if (env.DEMO === "true" || env.DEMO === 'TRUE' || env.DEMO === true || env.DEMO === "1" || env.DEMO === 1) {
      this.$store.dispatch("authcheck").then(res => {
        if (res.data.status === false && (this.theme == "frontend" || this.theme == "backend")) {
          this.$router.push({ name: "frontend.home" });
        };
      }).catch();
    }

  },
  watch: {
    $route(e) {
      if (e.meta.isFrontend === true) {
        this.theme = "frontend";
      } else if (e.meta.isTable === true) {
        this.theme = "table";
      } else {
        this.theme = "backend";
      }
    },
  },
};
</script>