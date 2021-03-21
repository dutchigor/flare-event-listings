<template>
    <div>
        <div class="row">
            <div class="col-12 col-lg-6">
                <img v-if="listing._embedded['wp:featuredmedia']"
                    class="card-img-top"
                    :src="listing._embedded['wp:featuredmedia'][0].source_url"
                    :alt="listing._embedded['wp:featuredmedia'][0].alt_text" />
            </div>
            <div class="col-12 col-lg-6">
                <p class="my-4" v-html="listing.cmb2.listing_details.post_content"></p>
                <p v-if="listing.cmb2.listing_details.fg_homepage">
                    <a :href="listing.cmb2.listing_details.fg_homepage">
                        <i class="fas fa-globe bg-circle d-40 bg-dark text-warning"></i>
                        {{ listing.cmb2.listing_details.fg_homepage }}
                    </a>
                </p>
            </div>
        </div>
        <div class="row">
            <div v-if="categories" class="col-12">
                <h5 class="mb-0">Categories</h5>
                <p>{{ categories }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        /* Listing to be displayed */
        listing: Object
    },
    computed: {
        /**
         * Transform terms in each listing category into a string of comma seperated names
         * @returns Array of strings of comma seperated names
         */
        categories() {
            return this.listing.categories.map( ctg => ctg.name ).join( ', ' )
        }
    },

    /**
     * Remove hash from url on closing the popup
     **/
    destroyed() {
        history.pushState("", document.title,
            window.location.pathname + window.location.search
        )
    }
}
</script>