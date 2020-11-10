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
                                <div class="input-group">
                                    <span class="input-group-addon" id="fromDate"><strong>From Date</strong></span>
                                    <Datetime 
                                        v-model="filters.fromDate"
                                        :input-class="'form-control'"
                                        type="datetime"
                                        :use12-hour="false"
                                        :format="'yyyy-MM-dd T'"
                                        :auto="true"
                                    />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon" id="toDate"><strong>To Date</strong></span>
                                    <Datetime 
                                        v-model="filters.toDate"
                                        :input-class="'form-control'"
                                        type="datetime"
                                        :use12-hour="false"
                                        :format="'yyyy-MM-dd T'"
                                        :auto="true"
                                    />
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
    import Vue from "vue";
    import axios from "axios";
    import JwPagination from "jw-vue-pagination";
    import Spinner from "./Spinner";
    import { Datetime } from 'vue-datetime';

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
        components: { Spinner, Datetime },
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

                // Format the selected from and to dates and send the formatted values
                const formatted_fromDate = await this.formatDate(fromDate);
                const formatted_toDate = await this.formatDate(toDate);

                const response = await axios.post(`logs/search`, {
                    query: this.query, 
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

            // Format the dates in 'yyyy-MM-dd H:mm' format
            formatDate(date) {
                // Exit if no date was selected
                if (!date) return;

                const dateObject = new Date(date);
                const year = dateObject.getFullYear();
                const month = dateObject.getMonth();
                const day = dateObject.getDate();
                const hour = dateObject.getHours();
                const minutes = dateObject.getMinutes();

                const formattedMonth = month <= 9 ? `0${month}` : month;
                const formattedDay = day <= 9 ? `0${day}` : day;
                const formattedHour = hour <= 9 ? `0${hour}` : hour;
                const formattedMinutes = minutes <= 9 ? `0${minutes}` : minutes; 

                // Fully formatted date to be returned
                const formattedDate = `${year}-${formattedMonth}-${formattedDay} ${formattedHour}:${formattedMinutes}`;

                return formattedDate;
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