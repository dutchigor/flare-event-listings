<template>
    <div class="main">
        <div class="row">
            <!-- One card for each service provider in the store -->
            <div class="col-12 col-sm-6 col-md-4 p-3"
                v-for="listing in $store.serviceProviders"
                :key="listing.id"
            >
                <div class="card">
                    <img v-if="listing._embedded['wp:featuredmedia']" class="card-img-top"
                        :src="listing._embedded['wp:featuredmedia'][0].source_url"
                        :alt="listing._embedded['wp:featuredmedia'][0].alt_text" />
                    <div class="card-body p-3 text-center">
                        <h5 class="card-title m-0">{{ listing.title.rendered }}</h5>
                        <p class="card-text my-3 mx-0">{{ listing.cmb2.listing_details.post_excerpt }}</p>
                        <a :href="'#'+listing.slug" class="stretched-link">
                            <i class="fas fa-arrow-circle-right"></i> Read more...
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Listing details modal -->
        <b-modal
            id="lst-details"
            :title="currentListing.title.rendered"
            size="xl"
            title-tag="h2"
            title-class="m-0 p-0"
            modal-class="fg-listings-app"
            hide-footer
            scrollable
            header-close-content="<i class='fas fa-times h1 m-0'></i>">
                <lst-details :listing="currentListing" />
        </b-modal>
    </div>
</template>

<script>
import LstDetails from "./LstDetails.vue";

export default {
    components: {
        LstDetails,
    },
    data() {
        return {
            /* Listing to be displayed */            
            currentListing: {
                title: { rendered: null },
            },
        }
    },
    methods: {
        /**
         * Set data.currentListing to the listing in the URL hash
         */
        setCurrentListing() {
            if ( !location.hash ) return

            const listing = this.$store.serviceProviders.find( ({ slug }) => {
                return slug == location.hash.substr(1)
            })

            if ( listing ) {
                this.currentListing = listing
                this.$bvModal.show( 'lst-details' )
            }
        }
    },
    /* Initiate component */
    async created() {
        // Fetch Service Providers from WordPress
        await this.$store.getServiceProviders()

        // Set current listing if initial url includes a hash
        this.setCurrentListing( this )

        // Set current listing on hash change
        window.addEventListener( 'hashchange', () => this.setCurrentListing() )
    },
}
</script>