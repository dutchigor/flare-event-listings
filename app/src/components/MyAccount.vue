<template>
    <div>
        <!-- Listings table -->
        <b-table striped :items="this.$store.usersListings" :fields="fields" stacked="sm">
            <template v-slot:cell(actions)="data">
                <button @click="view(data.item.id)" class="btn btn-primary mx-2">View</button>
                <button @click="edit(data.item.id)" class="btn btn-primary mx-2">Edit</button>
            </template>
        </b-table>
        <!-- Listing details/edit modal -->
        <b-modal
            id="lst-details"
            :title="currentListing.title.rendered"
            :size="( modalCmp == 'edit-listing' ) ? 'lg' : 'xl'"
            title-tag="h2"
            title-class="m-0 p-0"
            modal-class="fg-listings-app"
            hide-footer
            scrollable
            header-close-content="<i class='fas fa-times h1 m-0'></i>">
                <component :is="modalCmp" :listing="currentListing" />
        </b-modal>
    </div>
</template>

<script>
import LstDetails from './LstDetails'
import EditListing from './EditListing'

export default {
    components: {
        LstDetails,
        EditListing,
    },
    data() {
        return {
            /* Field settings for the listing table */
            fields: [
                { key: 'title.rendered', label: 'Name' },
                { key: 'cmb2.listing_details.post_excerpt', label: 'Title' },
                { key: 'categories', formatter: val => val.map( ctg => ctg.name ).join( ', ' ) },
                { key: 'status' },
                { key: 'actions' },
            ],

            /* Listing to be edited or displayed */
            currentListing: {
                title: { rendered: null },
            },

            /* Component to display in the listing details modal */
            modalCmp: null,
        }
    },
    methods: {
        /**
         * Show preview of the listing in the provided row in a popup.
         * @param {Number} rowId Identifier of the row to be displayed.
         */
        view( rowId ) {
            this.setCurrentListing( rowId )
            this.modalCmp = 'lst-details'
            this.$bvModal.show( 'lst-details' )
        },

        /**
         * Edit the listing in the provided row in a popup.
         * @param {Number} rowId Identifier of the row to be displayed.
         */
        edit( rowId ) {
            this.setCurrentListing( rowId )
            this.modalCmp = 'edit-listing'
            this.$bvModal.show( 'lst-details' )
        },

        /**
         * Update data.currentListing with the listing in the provided row.
         * @param {Number} rowId Identifier of the row take the listing from.
         */
        setCurrentListing( rowId ) {
            this.currentListing = this.$store.usersListings.find(({ id }) => {
                return id == rowId
            })
        },
    },

    /**
     * Fetch the current user's listings from the backend.
     */
    async beforeCreate() {
        await this.$store.getUsersListings()
    },
}
</script>