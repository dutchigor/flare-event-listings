<template>
  <div class="row fg-listings-app toggle-col">
    <div class="col">
      <component :is="$store.wpData.type" />
    </div>
    <div class="col-10 col-sm-4 col-md-3 toggleable bg-light px-0"
      :class="sidebarState"
      v-if="$store.wpData.type != 'MyAccount'"
    >
      <a class="fg-menu-toggle" @click="toggleSidebar">
        <i v-if="!smallScreen" class="fas fa-chevron-right fa-2x toggle-chevron"></i>
        <div v-else class="btn btn-primary btn-lg toggle-btn">Filter</div>
      </a>
      <side-bar />
    </div>
  </div>
</template>

<script>
import ProgramItems from './components/ProgramItems.vue'
import ServiceProviders from './components/ServiceProviders.vue'
import MyAccount from './components/MyAccount.vue'
import SideBar from './components/SideBar.vue'

export default {
  components: {
    ProgramItems,
    ServiceProviders,
    MyAccount,
    SideBar,
  },
  data() {
    return {
      /* Display mobile or desktop view. */
      smallScreen: true,

      /** State of the facets sidebar.
       * @values 'slide-in', 'slide-out', '' */
      sidebarState: 'slide-in',
    }
  },
  methods: {
    /**
     * Toggle the state of the facets sidebar.
     */
    toggleSidebar () {
      switch ( this.sidebarState ) {
        case '':
        case 'slide-out':
          this.sidebarState = 'slide-in'
          break;
        case 'slide-in':
          this.sidebarState = 'slide-out'
          setTimeout( () => this.sidebarState = '', 500 )
          break;
      }
    }
  },
  created() {
    /**
     * Get screen breakpoint from css and set data attributes based on this.
     * Add an event listener to reset data.smallScreen on screen resize.
     */
    const breakpointSm = getComputedStyle(document.documentElement)
      .getPropertyValue('--breakpoint-sm').replace(/\D/g, "")

    if ( breakpointSm <= screen.width ) {
      this.smallScreen = false
      this.sidebarState = ''
    }

    window.addEventListener("resize", () => this.smallScreen = breakpointSm > screen.width );
  }
}
</script>
