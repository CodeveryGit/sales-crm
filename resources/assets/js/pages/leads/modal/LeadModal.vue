<script>

    export default {
        props: ['dataLeadtypes', 'dataSelected'],

        data(){
            return {
                leadtypes: this.dataLeadtypes,
                selected_id: (this.dataSelected) ? this.dataSelected : this.dataLeadtypes[0].id,
            }
        },

        computed:{
            leadtype(){
                return this.leadtypes.find((a) => a.id == this.selected_id)
            }
        }
    }
</script>