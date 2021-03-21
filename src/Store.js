import Vue from "vue";
import axios from "axios";

/**
 * Vue instance as a lightweight global store.
 * Testing using a vue instance instead of Vuex
 */
export default new Vue({
    data: {
        /* Object coming from FG_Listing_App PHP class */
        wpData: window.listingsWpData,

        /* List of Service Providers as defined in the backend's REST API. */
        serviceProviders: [],

        /* List of Program Items as defined in the backend's REST API. */
        programItems: [],

        /* Taxonomy object containing list of terms */
        categories: {},

        /* Service Provider query used for filtering */
        spQuery: {},

        /* Program Item query used for filtering */
        piQuery: {},

        /* List of Listings owned by the current user */
        usersListings: [],
    },
    methods: {
        /**
         * Update data.categories with the Categories of the given type.
         * 
         * @param {String} type The category type
         * @param {Boolean} reload Wether to reload from the server or use cache
         */
        async getCategories( type, reload = false ) {
            if ( !(type in this.categories) || reload ) {
                const res = await axios.get(
                    this.wpData.rest_url + `/wp/v2/${type}`,
                    {
                        params: {
                            _fields: "id,name"
                        }
                    }
                )
                Vue.set( this.categories, type, res.data )
            }
    
            return this.categories[type]
        },

        /**
         * Get sanitized terms for taxonomy
         * 
         * @param {String} type The Category type
         */
        async getCbgCategories( type ) {
            const categories = await this.getCategories( type )
            return categories.map( ctg => {
                return {
                    value: ctg.id,
                    text: ctg.name,
                }
            })    
        },

        /**
         * Get Listings that match the given parameters
         * 
         * @param {Array} params Query parameters for the REST call
         */
        async getListings( params ) {
            const res = await axios.get(
                this.wpData.rest_url + '/wp/v2/listing', { 
                    params,
                    headers: { 'X-WP-Nonce': this.wpData.nonce }
                }
            )

            return res.data
        },

        /**
         * Sanitize the categories on a Listing
         * 
         * @param {Listing} lst Listing as defined by the backend REST API
         */
        setLstCtg( lst ) {
            const tax = `${lst.cmb2.listing_details.fg_type}_categories`
            this.$set( lst, 'categories', lst[tax].map( ctg => {
                return this.categories[tax].find( ({ id }) => {
                    return ctg == id
                } )
            }))
        },

        /**
         * Get Sanitized list of Service Providers matching the query in data.spQuery
         * 
         * @param {Boolean} reload Whether to reload from the server or use cache
         */
        async getServiceProviders( reload = false ) {
            if ( this.serviceProviders.length == 0 || reload ) {

                const params = Object.assign({}, this.spQuery )
                params.type = 'sp'
                params._embed = 1

                const sps = await this.getListings( params )
                
                await this.getCategories( 'sp_categories', true )
                sps.forEach( this.setLstCtg )

                this.serviceProviders = sps
            }
            
            return this.serviceProviders
        },

        /**
         * Get Sanetized list of Listings owned by the current user
         * 
         * @param {Boolean} reload Wether to reload from the server or use cache
         */
        async getUsersListings( reload = false ) {
            if ( this.usersListings.length == 0 || reload ) {
                const params = {
                    author: this.wpData.userid,
                    _embed: 1,
                }
                const listings = await this.getListings( params )

                await this.getCategories( 'sp_categories', true )
                await this.getCategories( 'pi_categories', true )
                listings.forEach( this.setLstCtg )

                this.usersListings = listings

            }

            return this.usersListings
        },

        /**
         * Set the query used to fetch Service Providers from the backend
         * 
         * @param {String} param Parameter to set on the query
         * @param {Any} val Value to set the parameter to
         */
        setSpQueryParam( param, val ) {
            this.$set( this.spQuery, param, val )
        },

        /**
         * Set the query used to fetch Program Items from the backend
         * 
         * @param {String} param Parameter to set on the query
         * @param {Any} val Value to set the parameter to
         */
        setPiQueryParam( param, val ) {
            this.$set( this.piQuery, param, val )
        },

        /**
         * Set Search parameter for the Service Provider and Program Item text search
         * 
         * @param {String} text Search parameter
         */
        setSearchText( text ) {
            this.setSpQueryParam( 'search', text )
            this.setPiQueryParam( 'search', text )
        },

        /**
         * Send updated listing to backend
         * 
         * @param {Object} listing Updated listing object
         */
        async updateListing( listing ) {
            const params = {
                _embed: 1,
            }
            const res = await axios.post(
                this.wpData.rest_url + `/wp/v2/listing/${listing.id}`,
                listing.body,
                {
                    params,
                    headers: { 'X-WP-Nonce': this.wpData.nonce }
                }
            )

            return res
        },
    },

    watch: {
        /* Persist spQuery in the local storage */
        spQuery: {
            deep: true,
            handler( query ) {
                localStorage.setItem( 'spQuery', JSON.stringify( query ) )
                this.getServiceProviders( true )
            }
        },

        /* Persist piQuery in the local storage */
        piQuery: {
            deep: true,
            handler( query ) {
                localStorage.setItem( 'piQuery', JSON.stringify( query ) )
                // this.getProgramItems( true )
            }
        }
    },

    created() {
        /* Set data.spQuery based on local storage if stored */
        const spQueryString = localStorage.getItem( 'spQuery' )
        if ( spQueryString ) this.spQuery = JSON.parse( spQueryString )

        /* Set data.piQuery based on local storage if stored */
        const piQueryString = localStorage.getItem( 'piQuery' )
        if ( piQueryString ) this.piQuery = JSON.parse( piQueryString )
        
    },
})