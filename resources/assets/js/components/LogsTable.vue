<template>
    <div class="row">
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
                                    <input type="text" class="form-control" name="fromDate" id="fromDate" aria-describedby="basic-addon3" style="background-color: white">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group date dtp">
                                    <span class="input-group-addon" id="toDate"><b>To Date</b></span>
                                    <input type="text" class="form-control" name="toDate" id="toDate" aria-describedby="basic-addon3" style="background-color: white">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <button v-on:click="search" class="btn btn-primary">Search</button>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                        <tr role="row">
                            <th tabindex="0" rowspan="1" colspan="1">Log Id</th>
                            <th tabindex="1" rowspan="1" colspan="1">User</th>
                            <th tabindex="2" rowspan="1" colspan="1">Action</th>
                            <th tabindex="3" rowspan="1" colspan="1">Subject</th>
                            <th tabindex="4" rowspan="1" colspan="1">Timestamp</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="hit in hits" :key="hit._id">
                                <td>{{hit._id}}</td>

                                <td v-if="hit._source.user.name != null"> {{hit._source.user.name}} </td>
                                <td v-else> {{hit._source.user.email}} </td>

                                <td>{{hit._source.action.name}}</td>
                                <td>{{hit._source.subject.tree}}</td>
                                <td>{{hit._source.created_at}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- DISPLAYED COUNT -->
                    <div>
                        <label>Showing {{stats.value}} of total {{stats.total}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    // TODO: Finish table and add filters
    // TODO: Make logic for creating the correct URL for search:
        // - add logic for handling sort value
        // - add logic for handling order value
        // - add logic for handling fromDate and toDate - how does it works??


    import axios from "axios";

    export default {
        data() {
            return {
                filters: {
                    query: "",
                    causer: "all",
                    cohort: "all",
                    action: "all",
                    activity: "all",
                    sort: "id",
                    order: "asc",
                    fromDate: null,
                    toDate: null,
                    user_id: null
                },
                hits: [],
                stats: {}
            }
        },
        mounted() {
            this.checkForUserID();
            this.search();
        },
        props: ['cohorts', 'actions', 'activities'],
        methods: {
            // Check and get the user ID from the query parameter if it exists
            checkForUserID() {
                const url = new URLSearchParams(window.location.search);
                const userID = parseInt(url.get("user_id"));

                this.filters.user_id = userID;
            },

            search: async function () {
                const { query, causer, cohort, action, activity, sort, order, fromDate, toDate, user_id } = this.filters;

                let url_filters = "";

                const queryFilter = query ? `&query=${query}` : '';
                const fromDateFilter = fromDate ? `&fromDate=${fromDate}` : '';
                const toDateFilter = toDate ? `&toDate=${toDate}` : '';

                // Check if user ID exits to use specific filters
                if (user_id) {
                    const defaultFilters = `?user_id=${user_id}&action=${action}&activity=${activity}&sort=${sort}&order=${order}`;

                    url_filters = `${defaultFilters}${queryFilter}${fromDateFilter}${toDateFilter}`;
                } else {
                    const defaultFilters = `?causer=${causer}&cohort=${cohort}&action=${action}&activity=${activity}&sort=${sort}&order=${order}`;

                    url_filters = `${defaultFilters}${queryFilter}${fromDateFilter}${toDateFilter}`;
                };
                

                const response = await axios.post(`logs/search${url_filters}`);

                this.hits = response.data.hits.hits;
                this.stats = response.data.hits.total;
                

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
            }
        }
    }

</script>

<style lang="scss" scoped>
.table {
    background-color: #fff;

    thead tr {
        th:nth-child(1) { width: 65px;  }
        th:nth-child(2) { width: 300px; }
        th:nth-child(3) { width: 85px;  }
        th:last-child { width: 120px;   }
    }

    tr td {
        padding: 8px !important;
        border: 1px solid #ddd;
    }

    tbody tr:nth-child(odd) { background-color: #ececec; }
}
</style>