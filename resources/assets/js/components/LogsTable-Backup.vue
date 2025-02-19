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
                        <div class="col-sm-12 filters">
                            <div>
                                <div class="form-group" v-if="filters.user_id === null">
                                    <label for="causer">Caused By</label>
                                    <select class="form-control" name="causer" id="causer" v-model="filters.causer">
                                        <option value="all">All</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                                <div class="form-group" v-if="filters.user_id === null">
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
                                    <div class="input-group">
                                        <span class="input-group-addon" id="fromDate"><strong>From Date</strong></span>
                                        <Datetime 
                                            v-model="filters.fromDate"
                                            input-id="startDate"
                                            :input-class="'form-control'"
                                            type="date"
                                            :format="'yyyy-MM-dd'"
                                            auto
                                            use12-hour
                                            :value-zone="'UTC'"
                                            :zone="'UTC'"
                                        >
                                            <label for="startDate" slot="after" class="input-group-addon input-group-addon--datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </label>
                                        </Datetime>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="toDate"><strong>To Date</strong></span>
                                        <Datetime 
                                            v-model="filters.toDate"
                                            input-id="endDate"
                                            :input-class="'form-control'"
                                            type="date"
                                            :format="'yyyy-MM-dd'"
                                            auto
                                            use12-hour
                                            :value-zone="'UTC'"
                                            :zone="'UTC'"
                                        >
                                            <label for="endDate" slot="after" class="input-group-addon input-group-addon--datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </label>
                                        </Datetime>
                                    </div>
                                </div>
                            </div>

                            <button v-on:click="search" class="btn btn-primary">Filter</button>
                        </div>
                    </div>

                    <div class="csv_search">
                        <form method="GET" action="logs/export">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input type="text" name="causer" :value="filters.causer" hidden>
                            <input type="text" name="cohort" :value="filters.cohort" hidden>
                            <input type="text" name="action" :value="filters.action" hidden>
                            <input type="text" name="activity" :value="filters.activity" hidden>
                            <input type="text" name="sort" :value="filters.sort" hidden>
                            <input type="text" name="order" :value="filters.order" hidden>
                            <input type="text" name="fromDate" :value="csv_fromDate" hidden>
                            <input type="text" name="toDate" :value="csv_toDate" hidden>
                            <input type="text" name="user_id" :value="filters.user_id" hidden>

                            <button type="submit" class="m-b-10 m-t-10" :disabled="hits.length === 0">Download CSV</button>
                        </form>

                        <div class="items-count">
                            <label for="itemsCount">Items per page:</label>
                            <select class="form-control" name="itemsCount" id="itemsCount" v-model="pageItemsCount">
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                                <option :value="500">500</option>
                                <option :value="1000">1000</option>
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
                        <!-- DISPLAYED COUNT -->
                        <div class="m-b-10">
                            <div class="m-b-20 m-t-20">
                                <label>Showing {{pageOfItems.length}} of total {{ new Intl.NumberFormat('en-US').format(stats.value) }} </label>
                                <p class="count-note" v-if="stats.total > 10000">
                                    <em>*In order to preserve the performance, maximum of 10,000 records are shown. Total number of records is:
                                        <strong>{{ new Intl.NumberFormat('en-US').format(stats.total) }}</strong>.
                                        You can get all entries by exporting the data to a .csv file.
                                    </em>
                                </p>
                            </div>
                            
                            <Pagination
                                :items="hits"
                                @changePage="onChangePage"
                                :pageSize="pageItemsCount"
                                :key="1"
                                :labels="customLabels"
                                @updatePagerDetails="updatePagerDetails"
                                :customPager="pager"
                                @updatePageOfItems="updatePageOfItems"
                            ></Pagination>
                        </div>
                        
                        <!-- TABLE -->
                        <div v-if="tableLoading">
                            <Spinner :modifierClass="'spinner--table'"/>
                        </div>
                        <table class="table table-bordered table-hover" :class="{'table--loading': tableLoading}">
                            <thead>
                            <tr role="row">
                                <th tabindex="0" rowspan="1" colspan="1" @click="sortTablePageItems('id')" :class="getColumnSortOrderClass('id')">
                                    Log Id
                                </th>

                                <th tabindex="1" rowspan="1" colspan="1" @click="sortTablePageItems('user')" :class="getColumnSortOrderClass('user')">
                                    User
                                </th>

                                <th tabindex="2" rowspan="1" colspan="1" @click="sortTablePageItems('action')" :class="getColumnSortOrderClass('action')">
                                    Action
                                </th>

                                <th tabindex="3" rowspan="1" colspan="1" class="table--subject-column" :class="getColumnSortOrderClass('subject')">
                                    Subject
                                </th>

                                <th tabindex="4" rowspan="1" colspan="1" @click="sortTablePageItems('timestamp')" :class="getColumnSortOrderClass('timestamp')">
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
                                    <td>{{formatTimestamp(hit._source.created_at)}}</td>
                                </tr>
                                <tr role="row">
                                <th tabindex="0" rowspan="1" colspan="1" @click="sortTablePageItems('id')" :class="getColumnSortOrderClass('id')">
                                    Log Id
                                </th>

                                <th tabindex="1" rowspan="1" colspan="1" @click="sortTablePageItems('user')" :class="getColumnSortOrderClass('user')">
                                    User
                                </th>

                                <th tabindex="2" rowspan="1" colspan="1" @click="sortTablePageItems('action')" :class="getColumnSortOrderClass('action')">
                                    Action
                                </th>

                                <th tabindex="3" rowspan="1" colspan="1" class="table--subject-column" :class="getColumnSortOrderClass('subject')">
                                    Subject
                                </th>

                                <th tabindex="4" rowspan="1" colspan="1" @click="sortTablePageItems('timestamp')" :class="getColumnSortOrderClass('timestamp')">
                                    Timestamp
                                </th>
                            </tr>
                            </tbody>
                        </table>

                        <!-- DISPLAYED COUNT -->
                        <div class="m-b-20 m-t-20">
                            <label>Showing {{pageOfItems.length}} of total {{ new Intl.NumberFormat('en-US').format(stats.value) }} </label>
                            <p class="count-note" v-if="stats.total > 10000">
                                <em>*In order to preserve the performance, maximum of 10,000 records are shown. Total number of records is:
                                    <strong>{{ new Intl.NumberFormat('en-US').format(stats.total) }}</strong>.
                                    You can get all entries by exporting the data to a .csv file.
                                </em>
                            </p>
                        </div>

                        <Pagination
                            :items="hits"
                            @changePage="onChangePage"
                            :pageSize="pageItemsCount"
                            :key="2"
                            :labels="customLabels"
                            @updatePagerDetails="updatePagerDetails"
                            :customPager="pager"
                            @updatePageOfItems="updatePageOfItems"
                        ></Pagination>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue from "vue";
    import axios from "axios";
    import JwPagination from "jw-vue-pagination";
    import Spinner from "./Spinner";
    import { Datetime } from 'vue-datetime';
    import moment from "moment";

    Vue.component('jw-pagination', JwPagination);

    import Pagination from "./Pagination"

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
                csrfToken: document.querySelector('meta[name="csrf-token"]').content,
                filters: {
                    causer: "all",
                    cohort: "all",
                    action: "all",
                    activity: "all",
                    sort: "timestamp",
                    order: "desc",
                    fromDate: null,
                    toDate: null,
                    user_id: null
                },
                csv_fromDate: null,
                csv_toDate: null,
                hits: [],
                stats: {},
                pageOfItems: [],
                pager: {}, // Pagination details
                pageItemsCount: 50,
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
        components: { Spinner, Datetime, Pagination },
        methods: {
            // Check and get the user ID from the query parameter if it exists
            checkForUserID() {
                const url = new URLSearchParams(window.location.search);
                const userID = parseInt(url.get("user_id"));

                if(!Number.isNaN(userID)){
                    this.filters.user_id = userID;
                }
            },

            search: async function () {
                // Set loading state
                this.tableLoading = true;
                
                // Desctructure filters
                const { causer, cohort, action, activity, sort, order, fromDate, toDate, user_id } = this.filters;

                // Format the selected from and to dates and send the formatted values
                const formatted_fromDate = fromDate ? moment.utc(fromDate).format("YYYY-MM-DD 00:00:00") : null;
                const formatted_toDate = toDate ? moment.utc(toDate).format("YYYY-MM-DD 00:00:00") : null;

                // State used for hidden inputs for the CSV export
                this.csv_fromDate = formatted_fromDate;
                this.csv_toDate = formatted_toDate;

                const response = await axios.post(`logs/search`, {
                    filters: {
                        causer,
                        cohort,
                        action,
                        activity,
                        sort,
                        order,
                        fromDate: formatted_fromDate ? formatted_fromDate : null,
                        toDate: formatted_toDate ? formatted_toDate : null,
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
            },
            
            // Format timestamps in US format
            formatTimestamp(date) {
                return moment(date).format("MM/DD/YYYY h:mm A");
            },

            // Update the details for the Pagination component
            updatePagerDetails(pager) {
                this.pager = pager;
            },

            updatePageOfItems(pageOfItems)  {
                this.pageOfItems = pageOfItems;
            }
        }
    }

</script>

<style lang="scss" scoped>
.filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
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

.count-note {
    font-size: 11px;
    color: #e1302a;
}

.input-group-addon--datetimepicker { 
    height: 34px;
    border-left: none;
}
</style>