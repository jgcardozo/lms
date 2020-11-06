<template>
    <div>
        <table class="table table-bordered table-hover dataTable" id="logTable">
            <thead>
            <tr role="row">
                <th tabindex="0" rowspan="1" colspan="1">Log Id</th>
                <th tabindex="1" rowspan="1" colspan="1">User</th>
                <th tabindex="2" rowspan="1" colspan="1">Action</th>
                <th tabindex="3" rowspan="1" colspan="1">Subject</th>
                <th tabindex="4" rowspan="1" colspan="1">Timestamp</th>
            </tr>
            </thead>
                <tr v-for="hit in hits" :key="hit._id">
                    <td>{{hit._id}}</td>

                    <td v-if="hit._source.user.name != null"> {{hit._source.user.name}} </td>
                    <td v-else> {{hit._source.user.email}} </td>

                    <td>{{hit._source.action.name}}</td>
                    <td>{{hit._source.subject.tree}}</td>
                    <td>{{hit._source.created_at}}</td>
                </tr>
            <tbody>
            </tbody>
        </table>
        <div>
            <label>Showing {{stats.value}} of total {{stats.total}}</label>
        </div>
    </div>
</template>

<script>

    // TODO: Finish table and add filters
    // TODO: Make logic for creating the correct URL for search


    import axios from "axios";

    export default {
        mounted() {
            this.search();
        },
        methods: {
            search: function () {
                // url = url+"causer=all&cohort=1&action=2&activity=all&sort=id&order=asc";
                // url = url+"?user_id=53079";

                axios.post('logs/search', {
                    filters: {
                         "causer": "all",    // admin, user or "all"
                         "cohort": 1,        // cohort ID or "all"
                         "action": 2,        // action ID or "all"
                         "activity": "all",  // activity ID or "all"
                         "sort": "id",       // po koja kolona se sortira
                         "order": "asc",     // asc or desc
                         "fromDate": null,   // filter From
                         "toDate": null,     // filter To
                         "user_id": null    // user ID or null
                    }
                }).then((response) => {
                    this.hits = response.data.hits.hits;
                    this.stats = response.data.hits.total;
                });
            }
        },
        data() {
            return {
                hits: [],
                stats: {}
            }
        }
    }

</script>