<template>
    <ul class="list-group list-group-flush">
        <li class="list-group-item bg-light"><h3 class="my-0">Search</h3></li>
        <li class="list-group-item bg-light">
            <div class="form-group form-check">
                <b-form-input v-model="search" lazy placeholder="Enter search terms" />
            </div>
        </li>
    </ul>
</template>

<script>
export default {

    computed: {
        /* Search term to filter the listings by */
        search: {
            get() {
                return this.$store.spQuery.search
            },
            set( val ) {
                this.$store.setSearchText( val )
            }
        },
    }
}
</script>