<script>
    import axios from 'axios';    

    export default {
        name: 'home',
        data: () => ({ timelineSteps: [] }),       
        computed: {            
        },
        methods: {      
        },
        mounted() {    
            axios.get('https://127.0.0.1:8000/timelinesteps').then(response => {
                console.log(response.data);
                this.timelineSteps = response.data;
                console.log("test");
            });        
        }
    }
</script>


<template>
    <h1>Home</h1>
    <ul v-for="timelineStep in timelineSteps" :key="timelineStep.id">
            {{ timelineStep.content }}
    </ul>
</template>

<style scoped>

</style>