<template>
    <!-- Renders to Li. Should be put in Ul in parent component -->
    <b-collapse v-model="show" tag="li" class="list-group-item bg-light">
        <div class="form-group form-check">
            <h6 class="my-3">Categories</h6>
            <b-checkbox-group
                v-model="$store.spQuery.sp_categories"
                :options="categories"
                stacked />
        </div>
    </b-collapse>
</template>

<script>
export default {
    props: {
        /* Toggle facet visibility. */
        show: Boolean,
    },
    data() {
        return {
            /* All service provider categories. */
            categories: []
        }
    },

    /**
     * Fetch the categories on loading this component
     */
    async created() {
        this.categories = await this.$store.getCbgCategories( 'sp_categories' )
    },
}
</script>