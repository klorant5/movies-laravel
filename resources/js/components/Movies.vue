<template>
    <div>
        <h2>Movies</h2>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a @click="fetchMovies(pagination.prev_page_url)"
                       class="page-link" href="#">Previous</a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link text-dark" href="#">Page {{ pagination.current_page }} of {{ pagination.last_page }}</a>
                </li>
                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a @click="fetchMovies(pagination.next_page_url)"
                       class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
        <div class="row">
            <div class="col-sm-3" v-for="movie in movies">
                <a href="#">
                    <div class="card">
                        <img v-bind:src="movie.poster_url" class="card-img-top" v-bind:alt="movie.title">
                        <div class="card-body">
                            <h5 class="card-title">{{ movie.title }}</h5>
                            <p class="card-text">{{ movie.description }}</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                movies: [],
                pagination: {},
            }
        },

        created() {
            this.fetchMovies();
        },

        methods: {
            fetchMovies(page_url) {
                let vm = this;
                page_url = page_url || 'api/v1/movies';
                fetch(page_url, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer eiwjgm436eroim46oi2m425m2or'
                    },
                }).then(res => res.json())
                    .then(res => {
                        this.movies = res.data;
                        vm.makePagination(res.meta,res.links);
                    })
                .catch(err => console.log(err));
            },
            makePagination(meta, links) {
                let pagination = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: links.next,
                    prev_page_url: links.prev,
                };
                this.pagination = pagination;
            }
        }
    }
</script>