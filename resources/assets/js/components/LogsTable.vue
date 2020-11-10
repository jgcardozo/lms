<template>
    <div v-if="pageLoading">
        <Spinner />
    </div>
    <div class="row" v-else>
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">
                    <!-- FILTERS -->
                    <div class="row form-inline m-b-20">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="causer">Caused By</label>
                                <select class="form-control" name="causer" id="causer" v-model="filters.causer">
                                    <option value="all">All</option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="cohort">Cohort</label>
                                <select class="form-control" name="cohort" id="cohort" v-model="filters.cohort">
                                    <option value="all">All</option>
                                    <option v-for="cohort in cohorts" :key="cohort.id" :value="cohort.id">{{ cohort.name }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="action">Action</label>
                                <select class="form-control" name="action" id="action" v-model="filters.action">
                                    <option value="all">All</option>
                                    <option v-for="action in actions" :key="action.id" :value="action.id">{{ action.name }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="activity">Activity</label>
                                <select class="form-control" name="activity" id="activity" v-model="filters.activity">
                                    <option value="all">All</option>
                                    <option v-for="activity in activities" :key="activity.id" :value="activity.id">{{ activity.name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                    <div class="input-group date dtp">
                                        <span class="input-group-addon" id="fromDate"><b>From Date</b></span>
                                        <input type="text" class="form-control" name="fromDate" id="fromDate" aria-describedby="basic-addon3" :v-bind="filters.fromDate"  style="background-color: white">
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group date dtp">
                                        <span class="input-group-addon" id="toDate"><b>To Date</b></span>
                                        <input type="text" class="form-control" name="toDate" id="toDate" aria-describedby="basic-addon3" v-bind="filters.toDate"  style="background-color: white">
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>

                            <button v-on:click="search" class="btn btn-primary">Search</button>
                        </div>
                    </div>

                    <div class="csv_search">
                        <div>
                            <input type="text" name="search" v-model="query" placeholder="Search" />
                        </div>

                        <div class="items-count">
                            <label for="itemsCount">Items per page:</label>
                            <select class="form-control" name="itemsCount" id="itemsCount" v-model="pageItemsCount">
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                        </div>

                    </div>

                    <div v-if="hits.length === 0">
                        <div class="no-data">
                            <p>No data available.</p>
                            <Spinner v-if="tableLoading" />
                        </div>
                    </div>

                    <div v-else>
                        <form method="get" action="logs/export">
                            <button type="submit" class="m-b-10 m-t-10" name="csv">CSV</button>
                        </form>
                        <!-- TABLE -->
                        <div v-if="tableLoading">
                            <Spinner :modifierClass="'spinner--table'"/>
                        </div>
                        <table class="table table-bordered table-hover" :class="{'table--loading': tableLoading}">
                            <thead>
                            <tr role="row">
                                <th 
                                    tabindex="0" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('id')"
                                    :class="getColumnSortOrderClass('id')"
                                >
                                    Log Id
                                </th>

                                <th 
                                    tabindex="1" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('user')"
                                    :class="getColumnSortOrderClass('user')"
                                >
                                    User
                                </th>

                                <th 
                                    tabindex="2" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('action')"
                                    :class="getColumnSortOrderClass('action')"
                                >Action</th>

                                <th 
                                    tabindex="3" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('subject')"
                                    :class="getColumnSortOrderClass('subject')"
                                >
                                    Subject
                                </th>

                                <th 
                                    tabindex="4" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('timestamp')"
                                    :class="getColumnSortOrderClass('timestamp')"
                                >
                                    Timestamp
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="hit in pageOfItems" :key="hit._source.id">
                                    <td>{{hit._source.id}}</td>

                                    <td v-if="hit._source.user.name != null"> {{hit._source.user.name}} </td>
                                    <td v-else> {{hit._source.user.email}} </td>

                                    <td>{{hit._source.action.name}}</td>
                                    <td>{{hit._source.subject.tree}}</td>
                                    <td>{{hit._source.created_at}}</td>
                                </tr>
                                <tr role="row">
                                <th 
                                    tabindex="0" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('id')"
                                    :class="getColumnSortOrderClass('id')"
                                >
                                    Log Id
                                </th>

                                <th 
                                    tabindex="1" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('user')"
                                    :class="getColumnSortOrderClass('user')"
                                >
                                    User
                                </th>

                                <th 
                                    tabindex="2" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('action')"
                                    :class="getColumnSortOrderClass('action')"
                                >Action</th>

                                <th 
                                    tabindex="3" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('subject')"
                                    :class="getColumnSortOrderClass('subject')"
                                >
                                    Subject
                                </th>

                                <th 
                                    tabindex="4" 
                                    rowspan="1" 
                                    colspan="1" 
                                    @click="sortTablePageItems('timestamp')"
                                    :class="getColumnSortOrderClass('timestamp')"
                                >
                                    Timestamp
                                </th>
                            </tr>
                            </tbody>
                        </table>

                        <!-- DISPLAYED COUNT -->
                        <div class="m-b-20 m-t-20">
                            <label>Showing {{pageOfItems.length}} of total {{stats.total}}</label>
                        </div>

                        <jw-pagination 
                            :items="hits"
                            @changePage="onChangePage"
                            :pageSize="pageItemsCount"
                            :key="pageItemsCount"
                            :labels="customLabels"
                        ></jw-pagination>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

    // TODO: Finish table and add filters
    // TODO: Make logic for creating the correct URL for search:
        // - add logic for handling sort value [x]
        // - add logic for handling order value [x]
        // - add sorting functionality [x]
        // - spinner and loading state [x]
        // - show message if no hits exist [x]
        // - add logic for handling fromDate and toDate - how does it works??
        // - CSV export dowlnload [x]
    // TODO: Pagination using jw-pagination [x]

    import Vue from "vue";
    import axios from "axios";
    import JwPagination from "jw-vue-pagination";
    import Spinner from "./Spinner";

    Vue.component('jw-pagination', JwPagination);

    // Custom labels for the pagination
    const customLabels = {
        first: "<<",
        last: ">>",
        previous: "<",
        next: ">"
    }

    export default {
        data() {
            return {
                query: "",
                filters: {
                    causer: "all",
                    cohort: "all",
                    action: "all",
                    activity: "all",
                    sort: "",
                    order: "asc",
                    fromDate: null,
                    toDate: null,
                    user_id: null
                },
                hits: [],
                stats: {},
                pageOfItems: [],
                pageItemsCount: 10,
                pageLoading: true,
                tableLoading: true,
                customLabels
            }
        },
        mounted() {
            this.checkForUserID();
            this.search();
        },
        props: ['cohorts', 'actions', 'activities'],
        components: { Spinner },
        methods: {
            // Check and get the user ID from the query parameter if it exists
            checkForUserID() {
                const url = new URLSearchParams(window.location.search);
                const userID = parseInt(url.get("user_id"));

                this.filters.user_id = userID;
            },

            search: async function () {
                // Set loading state
                this.tableLoading = true;
                
                // Desctructure filters
                const { causer, cohort, action, activity, sort, order, fromDate, toDate, user_id } = this.filters;

                const response = await axios.post(`logs/search`, {
                    query: this.query, 
                    filters: {
                        causer,
                        cohort,
                        action,
                        activity,
                        sort,
                        order,
                        fromDate,
                        toDate,
                        user_id
                    }
                });

                // Populate table if data exists
                if (response.data.hits) {
                    this.hits = response.data.hits.hits;
                    this.stats = response.data.hits.total;
                } else {
                    this.hits = [];
                    this.stats = {};
                }

                // Hide spinner and show the table
                this.pageLoading = false;
                this.tableLoading = false;
                

                // NOTE: Ke se izbrise
                // Example:
                // url = url+"causer=all&cohort=1&action=2&activity=all&sort=id&order=asc";
                // url = url+"?user_id=53079";
                // axios.post(`logs/search${url_filters}`, {
                //     // filters: {
                //     //      "causer": "all",    // admin, user or "all"
                //     //      "cohort": 1,        // cohort ID or "all"
                //     //      "action": 2,        // action ID or "all"
                //     //      "activity": "all",  // activity ID or "all"
                //     //      "sort": "id",       // po koja kolona se sortira
                //     //      "order": "asc",     // asc or desc
                //     //      "fromDate": null,   // filter From
                //     //      "toDate": null,     // filter To
                //     //      "user_id": null    // user ID or null
                //     // }
                // }).then((response) => {
                //     this.hits = response.data.hits.hits;
                //     this.stats = response.data.hits.total;
                // });
            },

            // Set the sort and order state
            sortTablePageItems(column) {
                if (column === this.filters.sort) {
                    this.filters.order = this.filters.order === "asc" ? "desc" : "";
                    this.filters.sort = this.filters.order === "" ? "" : this.filters.sort;
                } else {
                    this.filters.sort = column;
                    this.filters.order = "asc";
                }

                // Query the endpoint again
                this.search();
            },

            // Add or remove a sorting class based on selected table column
            getColumnSortOrderClass(column) {
                switch (true) {
                    case this.filters.order === 'asc' && this.filters.sort === column:
                        return 'table--sort-latest';
                    case  this.filters.order === 'desc' && this.filters.sort === column:
                        return 'table--sort-oldest';
                    default:
                        return 'table--sort-default';
                }
            },

            // Handle pagination and items displayed
            onChangePage(pageOfItems) {
                this.pageOfItems = pageOfItems;
            }
        }
    }

</script>

<style lang="scss" scoped>
.csv_search {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 10px 0;
}

.items-count {
    display: flex;
    justify-content: center;
    align-items: center;
    
    & label { width: 200px; }
}

.no-data {
    display: flex;
    justify-content: center;
    align-items: center;

    p {
        font-size: 18px;
        text-align: center;
        margin: 20px 0;
    }

    .half-circle-spinner { 
        width: 20px;
        height: 20px;
        position: relative;
        top: unset;
        left: unset;
        transform: translate(10px, -5px);

        .circle { border: calc(25px / 10) solid transparent; }
    }
}
</style>