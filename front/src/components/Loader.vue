<script>
    export default {
        name: 'loader',
        data: () => ({}),       
        computed: {            
        },
        methods: {      
        },
        mounted() {       
        }
    }
</script>


<template>
    <div class="load-row">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>  
</template>

<style scoped>
.load-row {
  width: 100px;
  height: 50px;
  line-height: 50px;
  text-align: center;
}

.load-row span {
  display: inline-block;
  width: 10px;
  height: 10px;
  background: var(--primary);
  border-radius: 50px;
  animation: up-down6 0.5s ease-in infinite alternate;
}

.load-row span:nth-child(2) {
  background: var(--loading-secondary) 50%;
  animation-delay: 0.16s;
}

.load-row span:nth-child(3) {
  background: var(--secondary) ;
  animation-delay: 0.32s;
}

.load-row span:nth-child(4) {
  background: var(--loading-fourth);
  animation-delay: 0.48s;
}

@keyframes up-down6 {
  0% {
    transform: translateY(-10px);
  }

  100% {
    transform: translateY(10px);
  }
}
</style>