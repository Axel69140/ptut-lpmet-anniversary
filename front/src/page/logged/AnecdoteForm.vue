<script>     
    import Footer from '../../components/Footer.vue';
    import { anecdoteService } from '../../services/anecdote.services';
    import { accountService } from '../../services/account.services';
    import Loader from '../../components/Loader.vue';

export default {
  name: 'anecdoteForm',
  data: () => ({
    users: [],
    content: '',
    idUser: 1,
    isLoading: true
  }),
  computed: {

  },
  methods: {
    async parameterAnecdote() {
      if (this.content !== undefined && this.content !== '') {
        await anecdoteService.createAnecdote({
          content: this.content,
          creator: this.idUser,
        });

        // reset content input
        this.content = '';
        alert("Votre anecdote à bien été pris en compte après sa validation elle apparaitra lors de l'évènement du 30ième anniversaire du département informatique.");
        this.$router.push('../event');
      }
    }
  },
  async mounted() {
    this.idUser = await accountService.getId();
    this.isLoading = false;
  },
  components: {
    Footer,
    Loader
  }
}
</script>


<template>
    <Loader :isLoading="isLoading" class="loader-basique" />
    <main v-if="!isLoading">
      <div>
        <h1>Proposer une anecdote</h1> 
        <div class="formulaire">
            <div class="information"><!--Récupérer via la version admin les p a mettre ici-->
                <p class="moreInformation">Les annecdotes sont anonymes</p>
                <p class="moreInformation">Elles seront projeté lors de l'évènement des 30ans du département information de Bourg-En-Bresse</p>
                <p class="moreInformation">Elles doivent avoir un lien avec l'IUT</p>
                <p class="moreInformation">Elles seront projeté lors de l'évènement des 30ans du département information de Bourg-En-Bresse</p>
            </div>
            <div class="divTextZone">
                <textarea class="textZone" rows="10" cols="100" v-model="content"></textarea>
            </div>
            <div class="sendButton">
                <button @click="parameterAnecdote()" class="btn-custom">Envoyer l'anecdote</button>
            </div>
        </div>
      </div>
    </main>

    <Footer class="footer" />
</template>

<style scoped>


.footer{
  position: inherit;
}
.moreInformation{
    margin: 0;
    color: #777777;
}
.formulaire{
    display: flex;
    flex-direction: column;
}

.divTextZone{
    margin: 20px 0;
}
.textZone{
    border-radius: 20px;
    padding: 1%;
    width: 60%;
    border: solid 4px var(--primary);
}

.textZone:focus-visible{
    outline: var(--primary);
}

@media (max-width: 800px) {
  .textZone{
      width: 80%;
  }
}

</style>