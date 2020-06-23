<template>
    <div>
        <h2 class="mb20">{{movie.title}}</h2>
        <div class="mb20">
            <img :src="movie.poster_url" alt="">
        </div>
        <div class="mb20">
            Votes: {{ movie.vote_average * 10 }}%
        </div>
        <div>
            <p>
                {{movie.description}}
            </p>
        </div>

        <PeopleListComponent :people="movie.cast"></PeopleListComponent>
        <PeopleListComponent :people="movie.crew" title="Crew"></PeopleListComponent>

    </div>
</template>
<script>
    import PeopleListComponent from './PeopleListComponent';
    export default {
        data() {
            return {
                movie_id: 0,
                movie: {}
            }
        },
        components: {
            PeopleListComponent
        },
        created() {
            this.movie_id = this.$route.params.id;
            fetch('api/v1/movies/' + this.movie_id, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer eiwjgm436eroim46oi2m425m2or'
                },
            }).then(res => res.json())
                .then(res => {
                    this.movie = res.data;
                    // console.log('CAST:' , res.data.cast);
                    // console.log(typeof res.data.cast);
                })
                .catch(err => console.log(err));
        },

    }
</script>
<style>
    .mb20 {
        margin-bottom: 20px;
    }
</style>