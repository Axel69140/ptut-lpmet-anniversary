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
            });        
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Timeline</h1>
        <ul class="allTimelineSteps">
            <li v-for="timelineStep in timelineSteps" :key="timelineStep.id" class="stepWithBorder">
                <div v-if="timelineStep.media_id" class="stepWithPicture">
                    <div class="headerStep">
                        <h2>{{ timelineStep.title }}</h2>
                        <p class="dateStep">{{ getMonthFromDateString(timelineStep.date) }}</p>
                    </div>
                    <div class="mainStep">
                        <div class="floatImage">
                            <img class="imageStep" src={{ timelineStep.media }}>
                        </div>
                        <p class="contentStep">{{ timelineStep.content }}</p>
                        
                    </div>
                </div>
                <div v-else class="stepWithoutPicture">
                    <div class="headerStep">
                        <h2>{{ timelineStep.title }}</h2>
                        <p class="dateStep">{{ getMonthFromDateString(timelineStep.date) }}</p>
                    </div>
                    <p class="contentStep">{{ timelineStep.content }}</p>
                </div>
                <div class="spans">
                    <span class="cercle"></span>
                    <span class="barre"></span>
                </div>
            </li>
        </ul>
    </main>
    
    <Footer class="footer" />
</template>

<style scoped>
    *{
        margin: 0;
        padding: 0;
        border: none;
    }

    .footer{
    }

    .stepWithoutPicture{
        background-color: #fff;
        border: 5px solid var(--third);
        border-radius: 10px;
        width: 90%;
        padding: 15px;
        margin: 0 20px;
    }

    .stepWithPicture{
        background-color: #fff;
        border: 5px solid var(--third);
        border-radius: 10px;
        width: 90%;
        padding: 15px;
        margin: 0 20px;
    }

    li{
        max-width: 50%;
        display: flex;
        flex-direction: column;
    }

    li:nth-child(odd){
        display: flex;
        flex-direction: row-reverse;
        margin-left: 50%;
    }

    li:nth-child(even){
        margin-right: 50%;
    }

    .stepWithBorder{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .dateStep{
        color: var(--primary);
    }

    .spans{
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 1px;
    }
    span.barre{
        content: "";
        border: var(--primary) solid 4px;
        height: 100%;
    }

    span.cercle{
        content: "";
        border: solid 5px;
        border-radius: 50px;
        color: var(--primary);
        background-color: var(--secondary);
        min-width: 40px;
        min-height: 40px;
    }

    .headerStep{
        display: flex;
        justify-content: space-between;
    }

    .imageStep{
        margin: 10px 20px 10px 0;
        max-width: 250px;
        max-height: 200px;
    }

    .floatImage{
        float: left;
    }

    .contentStep{
        font-family: Avenir, Helvetica, Arial, sans-serif;
        text-align: justify;
    }

    .mainStep{
        margin: 25px 0 0 0;
    }

</style>