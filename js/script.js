$(function(Backbone){

	var User = Backbone.Model.extend({

		defaults: function(){
			return {
				uid: 0,
				secsignid: null,
				twofa_enabled: false
			};
		},

		toggle: function(){
			this.save({twofa_enabled: !this.get("twofa_enabled")})
		}
	});


	var UserList = Backbone.Collection.extend({
		model: User,

		enabled: function(){
			return this.where({twofa_enabled: true});
		},

		comparator: 'uid'
	});

	var List = new UserList;

	var UserView = Backbone.View.extend({
		tagName: "li",
		el: $('#user-list'),
		template: _.template("<strong><%= name %></strong> Enabled: <%= twofa_enabled %>"),

		initialize: function() {
      		this.listenTo(this.model, 'change', this.render);
      		this.listenTo(this.model, 'destroy', this.remove);
    	},	
		render: function(){
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		clear: function() {
    	  this.model.destroy();
    	}	
	});

	List.create({uid: 0, secsignid: "testid1"});
	List.create({uid: 0, secsignid: "testid2"});
	List.create({uid: 0, secsignid: "testid3"});
	List.create({uid: 0, secsignid: "testid4"});
	document.findItemById("user-list").add(List);
});