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
                let url = "logs/search";
                let token = "DA70t2tJcQbInLozcF9ZtyYt8Mk8SToROZe1GyvL";

                // create the URL here and send request
                url = url+"?_token="+token+"&causer=all&cohort=1&action=2&activity=all&sort=id&order=asc";
                axios.get(url).then(response => this.hits = response.data.hits.hits)
            },
        },
        data() {
            return {
                hits: []
            }
        }
    }

</script>