<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';     

    export default {
        name: 'home',
        data: () => ({ timelineSteps: [] }),       
        computed: {   
            month: function(){

            }         
        },
        methods: {
            getMonthFromDateString(dateString) {
                const monthNumber = dateString.substring(5, 7);
                const months = [
                    'Janvier',
                    'Février',
                    'Mars',
                    'Avril',
                    'Mai',
                    'Juin',
                    'Juillet',
                    'Août',
                    'Septembre',
                    'Octobre',
                    'Novembre',
                    'Décembre'
                ];
                return months[parseInt(monthNumber, 10) - 1] + ' ' + dateString.substring(0,4);
            }
        },
        mounted() {    
            axios.get('https://127.0.0.1:8000/timelinesteps').then(response => {
                console.log(response.data);
                for (let i = 0; i < response.data.length -1; i++) {
                    if(response.data[i].date > response.data[i+1].date){
                        let tmp = response.data[i];
                        response.data[i] = response.data[i+1];
                        response.data[i+1] = tmp;
                        i=0;
                    }
                }
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
        <div v-if="timelineStep.media_id" class="stepWithPicture">
        </div>
        <div v-else class="stepWithoutPicture">
            <h2> {{ timelineStep.title }}</h2>
            <p>{{ getMonthFromDateString(timelineStep.date) }}</p>
            {{ timelineStep.content }}
        </div>
    </ul>
    </main>
    
    <Footer/>
</template>

<style scoped>
    ul div{
        border: 1px solid black;
    }
</style>