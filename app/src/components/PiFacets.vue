<template>
    <!-- Renders to Li. Should be put in Ul in parent component -->
    <b-collapse v-model="show" tag="li" class="list-group-item bg-light">
        <div class="form-group form-check">
            <h6>Categories</h6>            
            <b-checkbox-group
                v-model="$store.piQuery.pi_categories"
                :options="categories"
                stacked />
        </div>
        <div class="form-group form-check">
            <h6>Stages</h6>            
            <b-checkbox-group
                v-model="$store.piQuery.program_locs"
                :options="stages"
                stacked />
        </div>
        <div class="form-group form-check">
            <h6>Starts after</h6>
            <vue-timepicker :minute-interval="5" v-model="$store.piQuery.start"></vue-timepicker>
        </div>
        <div class="form-group form-check">
            <h6>Ends before</h6>
            <vue-timepicker :minute-interval="5" v-model="$store.piQuery.end"></vue-timepicker>
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
            /* All program item categories. */
            categories: [],

            /* All program stages. */
            stages:[],
        }
    },

    /**
     * Fetch the categories and stages on loading this component
     */
    async created() {
        this.categories = await this.$store.remotes.getCbgCategories( 'pi_categories' )
        this.stages = await this.$store.remotes.getCbgCategories( 'stages' )
    }
}
</script>