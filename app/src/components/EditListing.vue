<template>
    <b-form class="mx-5 my-3" @submit.prevent="submit">
        <!-- Name -->
        <b-form-group label="Name" label-for="name" label-size="lg">
            <b-form-text>Answer to question 'Who?' or 'What?' (max 50 digits).</b-form-text>
            <b-form-input id="name" v-model="edits.body.title"></b-form-input>
        </b-form-group>
        <!-- Title -->
        <b-form-group label="Title" label-for="title" label-size="lg">
            <b-form-text>Answer shortly to question 'What?' (max 70 digits).</b-form-text>
            <b-form-input id="title" v-model="edits.body.cmb2.listing_details.post_excerpt"></b-form-input>
        </b-form-group>
        <!-- Image -->
        <b-form-group label="Image" label-for="image" label-size="lg">
            <b-form-text>The image (please do not use a logo) has to be of good quality and you must
                have the right to use the image. We recommend that you do not use photo collages here,
                and do not add text, dates etc. on the image. Technical requirements: aspect ratio 1:1
                (square), picture size min. 420x420 pix, file type .jpg, .jpeg or .png. Only images
                meeting the minimum requirements will be published.</b-form-text>
            <p>To do: Add image cropper</p>
        </b-form-group>
        <!-- Description -->
        <b-form-group label="Description" label-for="description" label-size="lg">
            <b-form-text>Answer to questions 'Who?' and 'What?' more in details (max 1500 digits).
                Keep the paragraphs concise and use subtitles, in order to make it easy for users
                to read the content.</b-form-text>
            <p>To do: add WYSIWYG</p>
        </b-form-group>
        <!-- Homepage -->
        <b-form-group label="Homepage" label-for="homepage" label-size="lg">
            <b-form-text>Answer to question 'Who?' or 'What?' (max 50 digits).</b-form-text>
            <b-form-input id="homepage" type="url" v-model="edits.body.cmb2.listing_details.fg_homepage" />
        </b-form-group>
        <!-- Category proposals -->
        <b-form-group label="Category proposals" label-for="catproposals" label-size="lg">
            <b-form-text>The Listing owner can propose categories that are relevant for the Listing.
                The actual categories shown on the Listing will be defined centrally.</b-form-text>
            <b-checkbox-group
                id="catproposals"
                v-model="edits.body.cmb2.listing_details.fg_ctg_proposals"
                :options="categories"
                stacked/>
            <b-form-group label="Suggest your own category"
                label-for="addCtg" label-cols="6" class="col-6">
                <b-form-input id="addCtg" @change="addCtg"></b-form-input>
            </b-form-group>
        </b-form-group>
        <!-- Location -->
        <b-form-group label="Location" label-for="location" label-size="lg">
            <b-form-text>If you can't find your location using the search in the Basic tab,
                you can use the Advanced tab for more options.</b-form-text>
            <p>To do: add Location</p>
        </b-form-group>
        <!-- To do: Add additional information -->
        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Submit</button>
        <b-alert :show="submitState == 'success'" variant="success">Listing updated</b-alert>
        <b-alert :show="submitState && submitState != 'success'" variant="danger">
            {{ submitState }}
        </b-alert>
    </b-form>
</template>

<script>
export default {
    props: {
        /* Listing to edit */
        listing: Object
    },
    data() {
        return {
            /* Categories available for selection in fg_ctg_proposals. */
            categories: [],

            /* filter and mapping of listing for updating relevant fields in the backend. */
            edits: {
                id: this.listing.id,
                body: {
                    title: this.listing.title.rendered,
                    cmb2: {
                        listing_details: {
                            post_content: this.listing.cmb2.listing_details.post_content,
                            fg_homepage: this.listing.cmb2.listing_details.fg_homepage,
                            fg_ctg_proposals: this.listing.cmb2.listing_details.fg_ctg_proposals,
                            post_excerpt: this.listing.cmb2.listing_details.post_excerpt,
                        }
                    },
                }
            },

            /* Status of listing submission to backend */
            submitState: false,
        }
    },
    methods: {
        /**
         * Add category to data.categories list and mark it as checked in category proposals.
         * 
         * @param {String} val Category to add.
         */
        addCtg( val ) {
            this.categories.push( val )
            this.listing.cmb2.listing_details.fg_ctg_proposals.push( val )
        },

        /**
         * Submit changed listing to backend.
         */
        async submit() {
            try {
                await this.$store.updateListing( this.edits )
            } catch (e) {
                this.submitState = e
            } finally {
                this.submitState = 'success'
                await new Promise((resolve) => setTimeout(resolve, 1000))
                this.$bvModal.hide( 'lst-details' )
            }
        }
    },
    /**
     * Populate data.categories with Service Provider categories
     * plus any additional suggestions made in this listing.
     */
    async created() {
        const ctgObjs = await this.$store.getCategories(
            `${this.listing.cmb2.listing_details.fg_type}_categories`
        )
        const ctgs = ctgObjs.map(({ name }) => name )
            .concat( this.edits.body.cmb2.listing_details.fg_ctg_proposals )

        this.categories = Array.from( new Set( ctgs ) )
    },
}
</script>