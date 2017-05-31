/**
 * Created by Lew on 11/04/2017.
 */
$(function() {
    $('.video').collection({
        up: 'disabled',
        down: 'disabled',
        add: '<a href="#" class="btn btn-success"><span class="glyphicon glyphicon-plus-sign"></span> Video</a>',
        remove: '<a href="#" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>',
        add_at_the_end: true,
    });
});