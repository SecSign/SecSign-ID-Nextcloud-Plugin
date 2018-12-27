(function(OC, window, $, undefinded){
	'use strict';

	$(document).ready(function() {
		var Users = function(baseUrl){
			this._baseUrl = baseUrl;
			this._users = [];
		}

		Users.prototype = {
			get: function(index){
				return _users[i];
			},
			loadAll: function(){
				var deferred = $.Deferred();
				var self = this;
				$.get(this._baseUrl+'/id/').done(function (users){
					console.log(users);
					self._users = users;
					deferred.resolve();
				}).fail( function(){
					deferred.reject();
				});
				return deferred.promise();
			},
			getAll: function(){
				return this._users;
			}
		};

		var View = function(users) {
			this._users = users;
		}

		View.prototype = {
			renderContent: function() {
				var source = $('#content-tpl').html();
				var template = Handlebars.compile(source);
				var context = {users: this._users.getAll()};
				console.log(context);
				var html = template(context);

				$('#page').html(html);
			}
		};

		var users = new Users(OC.generateUrl('/apps/secsignid'));
		var view = new View(users);
		users.loadAll().done(function (){
			view.renderContent();
		}).fail(function (){
			alert('Failure');
		});
	});
})(OC,window,jQuery);