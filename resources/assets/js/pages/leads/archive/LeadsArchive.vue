<template>
    <div class="card-border">
        <div class="leads-header d-flex">
            <input v-bind:placeholder="$t('common.search')" class="search-input" type="text" v-model="filter">
        </div>

        <div class="leads">
            <table class="table-body">
                <thead>
                <tr>
                    <!--<th :class="sorted.name" @click="sorting('name')">lead name</th>-->
                    <!--<th :class="sorted.client_name" @click="sorting('client_name')">client</th>-->
                    <!--<th :class="sorted.manager_name" @click="sorting('manager_name')">manager</th>-->
                    <!--<th :class="sorted.lead_type_name" @click="sorting('lead_type_name')">leadtype</th>-->

                    <th>{{$t('leads.lead_name')}}</th>
                    <th>{{$tc('models.clients',0)}}</th>
                    <th>{{$tc('models.managers',0)}}</th>
                    <th>{{$tc('models.lead_types',0)}}</th>
                </tr>
                </thead>
                <paginate tag="tbody"
                          name="data"
                          :list="filtering"
                          class="paginate-list"
                          :per="10"
                          :key="filter">
                    <tr v-for="model in paginated('data')">
                        <td>
                            <a :href="`/leads/archive/${model.id}`" v-text="model.name"></a>
                        </td>
                        <td v-text="clientName(model.client) === 'No client'? $t('common.no_client') : clientName(model.client)"></td>
                        <td v-text="model.manager.name"></td>
                        <td v-text="model.lead_type_name"></td>
                    </tr>
                    <div v-if="data.length === 0">{{$t('common.no_relevant_results')}}</div>

                </paginate>
            </table>
            <paginate-links
                    for="data"
                    :limit="2"
                    :show-step-links="true"
                    :hide-single-page="true"
                    :step-links="{
            next: ' ',
            prev: '  '
            }"
            ></paginate-links>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['dataLeads'],

        data() {
            return {
                data: this.dataLeads,
                filter: '',
                paginate: ['data'],
                // sorted: {
                //     name: '',
                //     client_name: '',
                //     manager_name: '',
                //     lead_type_name: '',
                // }
            }
        },
        computed: {
            filtering() {
                return this.data.filter(model => model.name.toLowerCase().includes(this.filter.toLowerCase()))
            },
        },
        methods: {
            clientName(client){
                return (client) ? client.name : 'No client';
            }
            // sorting(column) {
            //     (this.sorted[column] !== 'sort-desc') ? this.sortDesc(column) : this.sortAsc(column);
            // },
            //
            // sortDesc(column) {
            //     this.sorted[column] = 'sort-desc';
            //     this.data.sort((a, b) => (a[column].toLowerCase() > b[column].toLowerCase()) ? 1 : -1);
            // },
            //
            // sortAsc(column) {
            //     this.sorted[column] = 'sort-asc';
            //     this.data.sort((a, b) => (a[column].toLowerCase() < b[column].toLowerCase()) ? 1 : -1);
            // },

        }
    }
</script>