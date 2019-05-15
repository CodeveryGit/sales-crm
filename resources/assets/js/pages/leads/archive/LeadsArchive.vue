<template>
    <div class="card-border">
        <div class="leads-header d-flex">
            <input v-bind:placeholder="$t('common.search')" class="search-input" type="text" v-model="filter">
        </div>

        <div class="leads">
            <table class="table-body">
                <thead>
                <tr>
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
        }
    }
</script>