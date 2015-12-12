define([], function() {

    var test = function(foo, bar) {
alert('blabla');
    };

    test.prototype.lala = function(foo) {
        alert(foo);
    };

    return test;
});