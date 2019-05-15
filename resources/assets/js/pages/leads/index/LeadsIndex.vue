<script>
    import DateRangePicker from '../../../components/DateRangePicker';

    export default {
        props: ['dataLeadTypes', 'dataLeads', 'dataStatuses'],
        components: {daterangepicker: DateRangePicker},
        data() {
            return {
                leadtypes: this.dataLeadTypes,
                statuses: this.dataStatuses,
                leads: this.dataLeads,
                leadtypeSelected: this.dataLeadTypes[0],
                manager: '',
                startDate: '',
                endDate: '',
                search: '',
                ajaxCome: true,
            }
        },

        created() {
            this.initStatuses();
        },
        methods: {
            searchLeads() {
                this.getLeads();
            },
            filterByLeadtype() {
                this.getLeads();
            },
            filterByDate(date) {
                this.startDate = date.start;
                this.endDate = date.end;
                this.getLeads();
            },
            showMoreLeads(statusPosition) {
                this.getLeads(statusPosition);
            },
            filterByManager() {
                this.getLeads();
            },
            getLeads(statusPosition = 'all') {
                if (!this.ajaxCome) {
                    return;
                }
                let result = this.setOffset(statusPosition);
                if (result.offset != 'end') {
                    this.ajaxCome = false;

                    axios.post('/ajax/leads', {
                        lead_type_id: this.leadtypeSelected.id,
                        current_status: statusPosition,
                        offset: result.offset,
                        manager_id: this.manager,
                        search: this.search,
                        date_start: this.startDate,
                        date_end: this.endDate,
                    }).then(function (response) {
                        if (statusPosition === 'all') {
                            this.statuses = response.data.statuses;
                            this.initStatuses();
                            this.leads = response.data.leads;
                        } else if (response.data.length == 0) {
                            this.statuses[result.statusesKey].offset = 'end';
                        } else {
                            this.leads = this.leads.concat(response.data);
                        }
                        this.ajaxCome = true;
                    }.bind(this));
                }
            },
            initStatuses() {
                this.statuses.sort((a, b) => a.position - b.position);

                this.statuses.forEach(function (v) {
                    v.offset = 4;
                });
            },
            setOffset(statusPosition) {
                let result = {
                    offset: 4,
                    statusesKey: 0,
                };
                this.statuses.forEach(function (v, k) {
                    if (statusPosition === 'all') {
                        v.offset = result.offset;
                    } else if (v.position == statusPosition) {
                        result.statusesKey = k;
                        result.offset = v.offset;
                        v.offset != 'end' ? v.offset += 4 : void 0;
                    }
                });
                return result;
            },
        }
    }
</script>