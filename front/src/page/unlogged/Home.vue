<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';     

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
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
    <h1>Home</h1>
    <ul v-for="timelineStep in timelineSteps" :key="timelineStep.id">
            {{ timelineStep.content }}
    </ul>
    </main>
    
    <Footer/>
</template>

<style scoped>

</style>