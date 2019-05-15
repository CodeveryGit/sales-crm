<template>
    <div class="lead-statuses">
        <div class="status"
             :class="{
        'passed': lead.current_status >= status.position,
        'current': lead.current_status === status.position
        }"
             v-for="status, i in statuses">
            <div class="status-name"
                 @click="openPopup(status)"
                 v-text="status.name"
                 data-toggle="modal"
                 :data-target="getPopupName(lead.current_status, status.position)"
            ></div>
            <div class="status-triangle"></div>
        </div>

        <div class="modal fade"
             id="status-modal"
             tabindex="-1"
             role="dialog"
             aria-labelledby="#status-modal"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <h4 class="text-center mb-3" v-text="status.name"></h4>
                    <div class="form-group">
                        <textarea class="form-control" v-model="comment"></textarea>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                        <button class="button button-green button-round button-tick mr-3" data-dismiss="modal"
                                @click="change(status)"></button>
                        <button class="button button-red button-round button-cross" data-dismiss="modal"></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade"
             id="last-status-modal"
             tabindex="-1"
             role="dialog"
             aria-labelledby="#last-status-modal"
             aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <a v-show="showModalCreatedProject" :href="'../projects/'+createdProjectId">{{$t('common.go_to_created_project')}}</a>
                                <button v-show="!showModalCreatedProject" class="button button-purple"  @click="showModalType('status')">{{$t('common.set') + ' ' + $tc('common.status' , 0).toLowerCase()}}</button>
                                <button v-show="!showModalCreatedProject" class="button button-purple"  @click="showModalType('project')" >{{$t('common.create') + ' ' +  $tc('models.projects' , 0).toLowerCase()}}</button>
                                <button class="button button-red button-round button-cross" data-dismiss="modal"></button>
                            </div>
                        </div>
                        <div class="container-fluid" v-show="showModalSetLastStatus">
                            <h4 class="text-center mb-3" v-text="status.name"></h4>
                            <div class="form-group">
                                <textarea class="form-control" v-model="comment"></textarea>
                            </div>
                            <div class="form-group d-flex justify-content-center">
                                <button class="button button-green button-round button-tick mr-3" data-dismiss="modal"
                                        @click="change(status)"></button>
                            </div>
                        </div>
                        <div class="container-fluid" v-show="showModalExistClient">
                            <div class="form-group">
                                <h4 class="text-center mb-3">{{$t('common.impossible_create_project')}}</h4>
                            </div>
                        </div>
                        <div class="container-fluid" v-show="showModalCreateProject">
                            <div class="form-group">
                                <input class="form-control" v-model="projectName">
                                <span class="invalid-feedback" style="display: block" v-html="saveProjectError"></span>
                            </div>
                            <div class="form-group d-flex justify-content-center">
                                <button class="button button-green button-round button-tick mr-3"  @click="createProject(status)" ></button>
                            </div>
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
                statuses: this.dataLead.statuses,
                lead: this.dataLead,
                status: '',
                comment: '',
                saveProjectError: '',
                showModalSetLastStatus: true,
                showModalCreateProject: false,
                showModalCreatedProject: false,
                showModalExistClient: false,
                projectName: this.dataLead.name,
                createdProjectId: false,
            }
        },

        created() {
            this.statuses.sort((a, b) => a.position - b.position);
        },
        watch: {
            projectName : function () {
                if( this.projectName != this.lead.name){
                    this.saveProjectError = '';
                }
            }
        },
        methods: {
            showModalType(type){
                if(type == 'status'){
                    this.showModalCreateProject = false;
                    this.showModalExistClient = false;
                    this.showModalSetLastStatus = true;
                }else if(type == 'project' && this.lead.client_id){
                    this.showModalSetLastStatus = false;
                    this.showModalCreateProject = true;
                }else if(type == 'project' && !this.lead.client_id){
                    this.showModalSetLastStatus = false;
                    this.showModalExistClient = true;
                }
            },
            getPopupName(current_status, position){
                var lastPosition = this.statuses.slice(-1).pop().position;
                if( current_status < position && lastPosition != position) {
                    return '#status-modal';
                }
                else if( current_status < position && lastPosition == position) {
                    return '#last-status-modal';
                }
            },
            openPopup(status) {
                this.status = status;
            },
            change(status) {
                axios.post('ajax/leads/' + this.lead.id + '/status', {
                    status_id: status.id,
                    position: status.position,
                    comment: this.comment,

                }).then(() => {
                    this.lead.current_status = status.position;
                });
            },
            createProject(status){
                axios.patch('ajax/projects/autocreate', {
                    name:  this.projectName,
                    client_id: this.lead.client_id,
                    payment_type: this.lead.payment_type,
                    manager_id: this.lead.manager_id,
                    comment: this.lead.comment,
                    potential_income: this.lead.payment_quantity,
                }).then((response) => {
                    this.change(status);
                    this.createdProjectId = response.data;
                    this.showModalCreatedProject = true;
                    this.showModalCreateProject = false;
                }).catch((err) =>{
                    this.saveProjectError = '';
                    Object.entries(err.response.data.errors).forEach(([key, val]) => {
                        this.saveProjectError += '<strong>'+val+'</strong></br>';
                    });
                });
            },
        }
    }
</script>