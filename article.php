<link type="text/css" rel="stylesheet" href="<?= site_url('css/panel/panel.css') ?>">
<link type="text/css" rel="stylesheet" href="<?= site_url('css/articles/articles.css') ?>">
<link type="text/css" rel="stylesheet" href="<?= site_url('css/standards/standards_panel.css') ?>">

<script type="text/javascript" src="<?=site_url('bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js')?>"></script>
<script type="text/javascript" src="<?=site_url('bower_components/angular-route/angular-route.min.js')?>"></script>
<script type="text/javascript" src="<?=site_url('online/js/revpoints.js')?>"></script>

<div id="articles-container" class="container" ng-app="StudiesWeekly">

	<div ng-controller="articles">

		<div>

		  <!-- Nav tabs -->
		  <ul class="nav nav-tabs nav-justified" id="articles-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#articles" aria-controls="articles" role="tab" data-toggle="tab"><i class="fa fa-bookmark"></i> Articles</a></li>
		    <li role="presentation"><a href="/online/testing/create/<?=$publication_id?>/<?=$unit_id?>#sub-tab-scores" aria-controls="test-scores"><i class="fa fa-file"></i> Test Scores</a></li>
		    <li role="presentation"><a href="/online/testing/create/<?=$publication_id?>/<?=$unit_id?>#sub-tab-settings" aria-controls="edit-test"><i class="fa fa-pencil"></i> Edit Test</a></li>
		    <li role="presentation"><a href="/online/testing/create/<?=$publication_id?>/<?=$unit_id?>#sub-tab-statistics" aria-controls="test-statistics"><i class="fa fa-pie-chart"></i> Test Statistics</a></li>
		  </ul>

		  <!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="articles">
				<div class="row">
					<div class="col-lg-4">
						<div class="panel panel-default" id="sidepanel">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a href="">
										<i class="fa fa-th-list"></i>
									</a>
									Articles
									<a href="#standards" class="pull-right">
										<i class="fa fa-archive"></i>
									</a>
								</h4>
							</div>

							<div class="list-group">
								<a href="#/articles/{{ article.article_id }}" class="list-group-item" ng-repeat="(index, article) in articles"
								ng-class="{active: isSelectedArticle(article.article_id), complete: pointsArticles[article.article_id]}">
									<span class="fa-stack fa-lg">
										<i class="fa fa-circle fa-stack-1x fa-inverse"></i>
										<i ng-show="pointsArticles[article.article_id]" class="fa fa-check-circle fa-stack-1x"></i>
										<i ng-show="!pointsArticles[article.article_id]" class="fa fa-circle-o fa-stack-1x"></i>
									</span>
									{{ article.article_title }}
								</a>
								<a href="/online/quiz/take/{{ test.id }}" class="list-group-item" ng-show="test.id">Take Test</a>
							</div>
						</div>
					</div>

					<div class="col-lg-8">

						<div ng-view></div>

						<!-- Unit standards -->
						<div class="panel panel-primary panel-toggle" id="standards">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a ng-click="standardsCollapsed = !standardsCollapsed">
										Unit Standards <span class="caret"></span>
									</a>
								</h4>
							</div>
							<div class="panel-body" uib-collapse="standardsCollapsed">
								<div class="unit-standards">
									<uib-tabset type="pills" active="0" ng-show="standards.length">
										<uib-tab heading="{{ standard.code }}" ng-repeat="standard in standards">
											{{ standard.text | limitTo: 300 }}
										</uib-tab>
									</uib-tabset>
									<p ng-show="!standards.length">There are no standards associated with this unit.</p>
								</div>
							</div>
						</div>

						<!-- Article standards -->
						<div ng-show="articleStandards.length">
							<div class="title-bar">
								<div class="pull-left collapse-btn" ng-click="articleStandardsCollapsed = !articleStandardsCollapsed">
									<i class="fa" ng-class="{'fa-chevron-down': !articleStandardsCollapsed, 'fa-chevron-right': articleStandardsCollapsed}" aria-hidden="true" style=""></i>
								</div>
								<div class="title-padding">
									Article Standards
								</div>
								<div style="clear:both;"></div>
							</div>
							<div uib-collapse="articleStandardsCollapsed">
								<div class="collapse-panel">
									<div class="unit-standards">
										<uib-tabset type="pills" active="0" ng-show="articleStandards.length">
											<uib-tab heading="{{ standard.code }}" ng-repeat="standard in articleStandards">
												{{ standard.text | limitTo: 300 }}
											</uib-tab>
										</uib-tabset>
										<p ng-show="!articleStandards.length">There are no standards associated with this article.</p>
									</div>
								</div>
							</div>
						</div>

						<!-- Online teacher edition -->
						<div ng-bind-html="ote | html"></div>

						<?=$resources_panel?>
					</div>
				</div>
			</div>
		    <div role="tabpanel" class="tab-pane" id="test-scores">...</div>
		    <div role="tabpanel" class="tab-pane" id="edit-test">...</div>
		    <div role="tabpanel" class="tab-pane" id="test-statistics">...</div>
		  </div>

		</div>

	</div>
</div>

<script>

var sw = angular.module('StudiesWeekly', ['ngRoute', 'ngAnimate', 'ngTouch', 'ui.bootstrap']);

sw.controller('articles', function($scope, $routeParams, $location, articleService, $http) {

	$scope.articleId = null;
	var firstLoad = true;

	articleService.getArticles()
	.then(function(articles) {
		firstLoad = false;
		$scope.articles = articles;
		$scope.updateArticle($scope.articleId);
	});

	$scope.$on('$routeChangeSuccess', function() {
		$scope.articleId = $routeParams.articleId;
		if(firstLoad == false) {
			$scope.updateArticle($scope.articleId);
		}
	});

	$scope.updateArticle = function(articleId) {
		if(articleId === null || articleId === undefined) {
			$scope.articleId = articleService.defaultArticle();
			//TODO: theres probably a better way to get default article if none is chosen.
			$location.path( "/articles/" + $scope.articleId);
		}
		else {
			$scope.article = articleService.getArticle(articleId);
			$scope.previousArticle = articleService.previousArticle(articleId);
			$scope.nextArticle = articleService.nextArticle(articleId);
			$scope.getOTE(articleId);
			$scope.hasArticlePoints(articleId);
			$scope.getArticleStandards(articleId);
		}

	}

	$scope.isSelectedArticle = function(articleId) {
		return $scope.articleId == articleId;
	}

	$scope.ote = "";
	$scope.getOTE = function(articleId) {
		$http.get('/online/content/panels/' + articleId)
		.then(function(response) {
			$scope.ote = response.data;
		});
	}

	$scope.test = {};
	$http.get('/online/quiz/index/json')
	.then(function(response) {
		angular.forEach(response.data.available_tests, function(test) {
			if(test.unit_id == <?=$unit_id?>) {
				$scope.test = test;
			}
		});
	});

	$scope.standards = [];
	$http.get('/online/standards/get_unit_standards/' + <?=$unit_id?>)
	.then(function(response) {
		$scope.standards = response.data;
	});

	$scope.articleStandards = [];
	$scope.getArticleStandards = function(articleId) {
		$http.get('/online/standards/get_article_standards/' + articleId)
		.then(function(response) {
			$scope.articleStandards = response.data;
		});
	}

	$scope.hasPoints = null;
	$scope.hasArticlePoints = function(article_id) {
		$http.get('/online/content/has_points_by_target/' + article_id)
		.then(function(response) {
			//note: this response is coming back as a string true or false, not bool
			$scope.hasPoints = response.data;
		});
	}

	$scope.setPoints = function(articleId) {
		$scope.pointsArticles[articleId] = true;
	}

	$scope.pointsArticles = {};
	$http.get('/online/units/points/' + <?=$unit_id?>)
	.then(function(response) {
		angular.forEach(response.data, function(points) {
			$scope.pointsArticles[points.container_id] = true;
		});
	});

});

sw.controller('resources', function($scope, $http) {
	$scope.resources = {};
	$http.get('/online/resource/get_resources_by_publication/<?=$publication_id?>')
	.then(function(response) {
		$scope.resources = response.data;
	});
});

sw.filter('html', function($sce) {
	return function(val) {
		return $sce.trustAsHtml(val);
	};
});

sw.filter('audioUrl', function($sce) {
	return function(val) {
		return $sce.trustAsResourceUrl(val);
	};
});

sw.service('articleService', function($http, $q) {

	var _articles = {};
	var _articleIndex = [];

	this.getArticles = function() {
		var deferred = $q.defer();

		$http.get('/online/articles/get_articles_by_sku/<?=$publication->sku?>')
		.then(function(articles) {
			//add articles for just the unit
			var i = 0;
			angular.forEach(articles.data.data, function(article) {
				if(article.unit_id == <?=$unit_id?>) {
					_articles[article.article_id] = article;
					_articles[article.article_id]['index'] = i;
					_articleIndex.push(article.article_id);
				}
			});
			_articleIndex.sort(function(a, b) {
				return a - b;
			});

			deferred.resolve(_articles);
		});

		return deferred.promise;
	}

	this.getArticle = function(articleId) {
		return _articles[articleId];
	}

	this.previousArticle = function(articleId) {
		var index = _articleIndex.indexOf(articleId) - 1;
		var previousId = _articleIndex[index];
		if(previousId != undefined) {
			return _articles[previousId];
		}
		return false;
	}

	this.nextArticle = function(articleId) {
		var index = _articleIndex.indexOf(articleId) + 1;
		var nextId = _articleIndex[index];
		if(nextId != undefined) {
			return _articles[nextId];
		}
		return false;
	}

	this.defaultArticle = function() {
		return _articleIndex[0];
	}
});

sw.config(function ($routeProvider) {
	$routeProvider.
	when('/articles/:articleId', {
		templateUrl: '<?=$unit_id?>/articles/'
	}).
	otherwise('/', {
		template: "I'm the default"
	});
});

</script>
<script type="text/javascript" src="<?=site_url('online/js/articles/rev-points.js')?>"></script>
<script type="text/javascript" src="<?=site_url('online/js/articles/article-audio.js')?>"></script>
