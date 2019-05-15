<template>
    <div>
        <div class="history-button">
            <button class="button button-purple"
                    @click="show = true">
                    {{$t('common.see_history')}}
            </button>
        </div>
        <div class="lead-history card-border" v-show="show">
            <div class="history-header">
                <h3 class="history-title">{{$t('common.history')}}</h3>
                <button class="button button-round button-red button-cross"
                        @click="show = false"></button>
            </div>
            <hr>
            <div class="history-logs">
                <div v-for="log in logs" class="history-log">
                    <div class="log-circle"></div>
                    <div class="log-block card-border">
                        <div class="log-name" v-text="log.status_name"></div>
                        <div class="log-comment" v-text="log.comment ===  'Lead created' ? $t('leads.lead_created') : log.comment"></div>
                        <div class="log-date label-gray">
                            <i class="icon-history-date"></i>
                            <span v-text="log.created_at"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['dataLead'],
        data() {
            return {
                show: false,
                logs: '',
                lead: this.dataLead,
            }
        },

        computed: {
            current_status() {
                return this.lead.current_status;
            }
        },

        created() {
            this.refresh();
        },

        watch: {
            current_status() {
                this.refresh();
            },
        },

        methods: {
            refresh() {
                axios.get('ajax/leads/' + this.lead.id + '/logs')
                    .then((response) => this.logs = response.data);
            },
        }
    }
</script>