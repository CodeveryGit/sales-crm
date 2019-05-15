<script>
    export default {
        props: ['dataLead', 'dataFields'],

        data() {
            return {
                lead: this.dataLead,
                allFields: this.dataFields,
                editing: [],
                old: '',
            }
        },

        methods:{
            edit(item) {
                console.log(this.editing);
                this.old = item.value;
                this.editing.push(item);
            },

            save(item) {
                axios.patch(`ajax/leads/fields/update/${this.lead.id}`, item)
                    .then(() => {
                        let index = this.editing.indexOf(item);
                        if (index !== -1) this.editing.splice(index, 1);
                    });
            },

            add(item, i) {
                this.items.splice(i+1, 0, new_item);
                this.editing.push(new_item);
            },

            cancel(item) {
                item.value = this.old;

                let index = this.editing.indexOf(item);
                if (index !== -1) this.editing.splice(index, 1);
            },
        }

    }
</script>