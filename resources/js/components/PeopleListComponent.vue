<template>
    <div>
        <h3>{{ title }}</h3>
        <div>
            <div class="row">
                <div class="col-sm-3" v-for="actor in people">
                    <div>
                        <img :src="actor.image_url" alt="">
                    </div>
                    <div>
                        <div><strong>{{ actor.name }}</strong></div>
                        <div>{{ actor.role }}</div>
                    </div>
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