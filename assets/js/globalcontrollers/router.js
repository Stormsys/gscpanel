define(function() {
	var Router = function() {
			var lastHash = undefined;
			var parseHash = function(newHash, oldHash) {
					lastHash = oldHash;
					crossroads.parse(newHash);
				}

			this.goBack = function() {
				hasher.setHash(lastHash);
			};

            hasher.initialized.add(parseHash);
            hasher.changed.add(parseHash);

            hasher.init();
		}
	return new Router();
});