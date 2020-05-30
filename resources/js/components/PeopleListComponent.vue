<template>
    <div>
        <h3 class="mb20">{{ title }}</h3>
        <div>
            <div class="row">
                <div class="col-sm-3" v-for="actor in people">
                    <router-link :to="{name: 'actor_page', params: {id: actor.id}}" class="actor-link">
                        <div>
                            <img :src="actor.image_url" alt="">
                        </div>
                        <div class="name-and-roles">
                            <div><strong>{{ actor.name }}</strong></div>
                            <div>{{ actor.role }}</div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            people: {
                type: Object,
                required: false
            },
            title: {
                type: String,
                required: false,
                default: 'Cast'
            },

        },
        data() {
            return {
                people_data: []
            }
        },
    }
</script>
<style>
    .actor-link img {
        width: 100%;
    }
    .row a {
        display: block;
        text-decoration: none;
        margin-bottom: 20px;
    }
</style>