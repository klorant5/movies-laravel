<template>
    <div>
        <h3>{{ actor.name }}</h3>
        <div>
            <img :src="actor.image_url" :alt="actor.name">
        </div>
        <p>{{ actor.description }}</p>

        <div>
            <h3>Movie roles</h3>
            <div class="row">
                <div class="col-sm-3" v-for="movie in actor.movies">
                    <router-link :to="{name: 'movie_page', params: {id: movie.id}}">
                        <div>
                            <div>
                                <img v-bind:src="movie.poster_url" class="card-img-top" v-bind:alt="movie.title">
                            </div>
                            <div class="name-and-roles">
                                <div><strong>{{ movie.title }}</strong></div>
                                <div>{{ movie.role }}</div>
                            </div>
                        </div>
                    </router-link>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                actor: ''
            }
        },
        created() {
            this.movie_id = this.$route.params.id;
            fetch('api/v1/people/' + this.movie_id, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer eiwjgm436eroim46oi2m425m2or'
                },
            }).then(res => res.json())
                .then(res => {
                    console.log(res.data);
                    this.actor = res.data;
                })
                .catch(err => console.log(err));
        },
    }
</script>
<style>
    .name-and-roles {
        margin-top: 10px;
    }
</style>