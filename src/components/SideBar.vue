<template>
    <div class="side-menu">
        <!-- Text search section -->
        <search-facet />
        <!-- Service provider section -->
        <ul class="list-group list-group-flush">
            <li class="list-group-item bg-light d-flex justify-content-between align-items-baseline"
                v-if="$store.wpData.type == 'Map'"
            >
                <h3 class="my-0">Service providers</h3>
                <b-form-checkbox v-model="showSpFacets"
                    name="showSP" class="box-right"
                >Include</b-form-checkbox>
            </li>
            <sp-facets v-if="[ 'ServiceProviders', 'Map' ].includes( $store.wpData.type )"
                :show="showSpFacets" />
        </ul>
        <!-- Program Item section -->
        <ul class="list-group list-group-flush">
            <li class="list-group-item bg-light d-flex justify-content-between align-items-baseline"
                v-if="$store.wpData.type == 'Map'"
            >
                <h3 class="my-0">Program items</h3>
                <b-form-checkbox v-model="showPiFacets"
                    name="showSP" class="box-right"
                >Include</b-form-checkbox>
            </li>
            <pi-facets v-if="[ 'ProgramItems', 'Map' ].includes( $store.wpData.type )" 
                :show="showPiFacets"/>
        </ul>
    </div>
</template>

<script>
import SearchFacet from './SearchFacet.vue'
import SpFacets from './SpFacets.vue'
import PiFacets from './PiFacets.vue'

export default {
    components: {
        SearchFacet,
        SpFacets,
        PiFacets,
    },
    data() {
        return {
            /* Show service provider facets component. */
            showSpFacets: true,

            /* Show program item facets component */
            showPiFacets: true,
        }
    }
}
</script>