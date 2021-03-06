<?php
	$this->title = 'Elastica Documentation - Elastica.io';
?>
	<p>
		<em><a href="http://github.com/ruflin/Elastica" title="Elastica">Elastica</a></em> is a PHP client for the <em><a href="http://elasticsearch.org">elasticsearch</a></em> search engine/database. <em>Elasticsearch</em> is an Open Source (Apache 2), Distributed, RESTful, Search Engine built on top of Apache Lucene.
	</p>
	<p>
		<em>Elastica</em> is open source, and you can download or clone the source code from <a href="http://github.com/ruflin/Elastica">ruflin/Elastica</a>.
	</p>
	<p>
		These instructions give you an overview of how to use <em>Elastica</em>. You can find the complete <strong>API</strong> <a href="api/index.html">here</a>. It is build directly from source.
	</p>

	<nav class="toc">
		<ul>
			<li><a href="/en/releases">Releases</a></li>
			<li>
				<a href="#section-installation">Installation</a>
				<ul>
					<li>
						<a href="#section-download">Download</a>
					</li>
					<li>
						<a href="#section-include">Include in your project</a>
					</li>
					<li>
						<a href="#section-connect">Connect to elasticsearch</a>
						<ul>
							<li><a href="#section-connect-single">Running a single node</a></li>
							<li><a href="#section-connect-cluster">Running a cluster</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li>
				<a href="#section-storing">Storing and indexing documents</a>
				<ul>
					<li>
						<a href="#section-analysis">Define Analysis</a>
					</li>
					<li>
						<a href="#section-mapping">Define Mapping</a>
					</li>
					<li>
						<a href="#section-add">Add documents</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="#section-search">Search documents</a>
				<ul>
					<li>
						<a href="#section-query">Search query</a>
					</li>
					<li>
						<a href="#section-retrieve">Retrieve results</a>
					</li>
					<li>
						<a href="#section-filter">Add filter</a>
					</li>
					<li>
						<a href="#section-facets">Facets</a>
					</li>
				</ul>
			</li>
			<li><a href="#section-credits">Credits</a></li>
		</ul>
	</nav>

	<h2 id="section-installation">Installation</h2>
		<h3 id="section-download">Download</h3>
			<p>
				You can download this project in either <a href="http://github.com/ruflin/Elastica/zipball/master">zip</a> or <a href="http://github.com/ruflin/Elastica/tarball/master">tar</a> formats.
			</p>
			<p>
				The prefered way is to clone <em>Elastica</em> with <a href="http://git-scm.com">Git</a> by running:
				<pre class="prettyprint">$ git clone git://github.com/ruflin/Elastica</pre>
			</p>
		<h3 id="section-include">Include in your project</h3>
			<p>
				To use Elastica in your project you have to include it. Best way to do this is to use PHP <a href="http://www.php.net/manual/en/language.oop5.autoload.php">autoload</a>. This function is automatically called in case you are trying to use a class/interface which hasn't been defined yet.
			</p>
			<p>
				So let's assume you installed Elastica to <code>/var/www/Elastica</code>. When you make an instance of <code>Elastica_Client</code>, the function will check if there is a file with that class in <code>/var/www/Elastica/Client</code> and load it.
			<p>
			<pre class="prettyprint">
function __autoload_elastica ($class) {
$path = str_replace('_', '/', $class);

if (file_exists('/var/www/' . $path . '.php')) {
require_once('/var/www/' . $path . '.php');
}
}
spl_autoload_register('__autoload_elastica');</pre>
			<p>
				Now you are ready to use elasticsearch in your PHP project! Hurray!
			</p>
		<h3 id="section-connect">Connect to elasticsearch</h3>
				<p>
					You need at least one instance, also called node, of elasticsearch running and be able to connect to it. Luckily it's easy and you can follow this guide to <a href="http://www.elasticsearch.org/tutorials/2010/07/01/setting-up-elasticsearch.html">set up elasticsearch</a>.
				</p>
				<p>
					To start an instance of your installed elasticsearch you just need to run the following command in the elasticsearch folder:
				</p>
				<pre>$ ./bin/elasticsearch -f</pre>
				<p>
					You can start multiple nodes by running the command multiple times. As you will see, the first node will be started on port 9200. If you start another node it will listen on port 9201 and so on. Elasticsearch automatically discovers the other nodes and creates a cluster.
				</p>
				<h4 id="section-connect-single">Running a single node</h4>
					<p>
						When your single elasticsearch node is running on <code>localhost:9200</code>, which is the default, you can simply connect:
					</p>
					<pre class="prettyprint">$elasticaClient = new Elastica_Client();</pre>
					<p>
						In case that your node is running on another server or a different port, you can pass these information as an array:
					</p>
					<pre class="prettyprint">
$elasticaClient = new Elastica_Client(array(
'host' => 'mydomain.org',
'port' => 12345
));</pre>
				<h4 id="section-connect-cluster">Running a cluster</h4>
					<p>
						Elasticsearch was built with the cloud / multiple distributed servers in mind. It is quite easy to start a elasticsearch cluster simply by starting multiple instances of elasticsearch on one server or on multiple servers. To start multiple instances of elasticsearch on your local machine, just run the command to start an instance in the elasticsearch folder twice.
					</p>
					<p>
						One of the goals of the distributed search index is availability. If one server goes down, search results should still be served. But if the client connects to only the server that just went down, no results are returned anymore. Because of this, <code>Elastica_Client</code> supports multiple servers which are accessed in a round robin algorithm. This is the only and also most basic option at the moment. So if we start a node on port 9200 and port 9201 above, we pass the following arguments to <code>Elastica_Client</code> to access both servers:
					</p>
					<pre class="prettyprint">
$elasticaClient = new Elastica_Client(array(
'servers' => array(
array('host' => 'localhost', 'port' => 9200)
array('host' => 'localhost', 'port' => 9201)
)
));</pre>
					<p>
						This client implementation also allows to distribute the load on multiple nodes. As far as I know, Elasticsearch already does this quite well on its own. But it helps if more than one node can answer http requests. Therefore, the method above is really useful if you use more than one elasticsearch node in a cluster to send your request to all servers.
					</p>

	<h2 id="section-storing">Storing and indexing documents</h2>
		<p>
			To add data to the index you can just drop some documents in and it will be indexed directly. But in most cases you want to specify how your data is indexed. There are a lot of possibilities in elasticsearch to do so. You can decide how each field is mapped and how your data is analyzed to provide the full text search.
		</p>
		<p>
			For more information and all the possibilities elasticsearch provides, take a look at the <a href="http://www.elasticsearch.org/guide/reference/index-modules/analysis/">Analysis</a> and the <a href="http://www.elasticsearch.org/guide/reference/mapping/">Mapping</a> reference.
		</p>
		<p>
			The documents in elasticsearch are organized in indexes. Each index contains one or more types which contains the documents. So to put our data in elasticsearch, we first have to define how the index and the type will look like.
		</p>
		<h3 id="section-analysis">Define Analysis</h3>
			<p>
				In elasticsearch, when you create an index, you define the number of shards and number of replicas. A shard is a part of your data and a replica is like an backup of that data. So when you have one node, all the shards and all the replicas will be on that node. When you have more nodes, your data will be balanced to these nodes. How it is balanced depends on your configuration. More on this topic can be found <a href="http://www.elasticsearch.org/videos/2010/02/08/es-distributed-diagram.html"> here</a>.

			</p>
			<p>
				Data in elasticsearch is analyzed at two different times. Once, when you index a document it's analyzed and this information is put in the index. The other time is when do a search. Elasticsearch analyzes the search query and looks up the gained information in the index. To see all possible analyzers and filter check out the <a href="http://www.elasticsearch.org/guide/reference/index-modules/analysis/">Analysis</a> reference.
			</p>
			<p>
				Let's create an index called twitter! So in the <code>indexAnalyzer</code> we define how the data will be analyzed when it's indexed and then we define how elasticsearch will analyze the search query in the <code>searchAnalyzer</code>. In this example we'll also use a custom snowball filter for the data.
			</p>
<pre class="prettyprint">
// Load index
$elasticaIndex = $elasticaClient->getIndex('twitter');

// Create the index new
$elasticaIndex->create(array(
'number_of_shards' => 4,
'number_of_replicas' => 1,
'analysis' => array(
'analyzer' => array(
	'indexAnalyzer' => array(
		'type' => 'custom',
		'tokenizer' => 'standard',
		'filter' => array('lowercase', 'mySnowball')
	),
	'searchAnalyzer' => array(
		'type' => 'custom',
		'tokenizer' => 'standard',
		'filter' => array('standard', 'lowercase', 'mySnowball')
	)
),
'filter' => array(
	'mySnowball' => array(
		'type' => 'snowball',
		'language' => 'German'
	)
)
)
), true);</pre>
		<h3 id="section-mapping">Define Mapping</h3>
			<p>
				The Mapping defines what kind of data is in which field. If no mapping is defined, elasticsearch will guess the kind of the data and map it. To see all possibilities, check out the <a href="http://www.elasticsearch.org/guide/reference/mapping/">Mapping</a> reference.
			</p>
			<p>
				In our example, we will create an type called tweet which is in our index twitter. So first we create that type and afterwards we define the mapping. Note that it is possible to boost data in elasticsearch. You can either boost a specific field or you can boost a complete document. To boost a document, we'll use the field <code>_boost</code>. If we boost a field it's defined just like the kind of the field.
			</p>
<pre class="prettyprint">
// Load type
$elasticaType = $elasticaIndex->getType('tweet');

// Define mapping
$mapping = new Elastica_Type_Mapping();
$mapping->setType($elasticaType);
$mapping->setParam('index_analyzer', 'indexAnalyzer');
$mapping->setParam('search_analyzer', 'searchAnalyzer');

// Define boost field
$mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

// Set mapping
$mapping->setProperties(array(
'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
'user'    => array(
'type' => 'object',
'properties' => array(
	'name'      => array('type' => 'string', 'include_in_all' => TRUE),
	'fullName'  => array('type' => 'string', 'include_in_all' => TRUE)
),
),
'msg'     => array('type' => 'string', 'include_in_all' => TRUE),
'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE),
'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE),
'_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
));

// Send mapping to type
$mapping->send();</pre>
		<h3 id="section-add">Add documents</h3>
			<p>
				Now that we have our index ready for the data, we just need to go ahead an put some data in there!
			</p>
			<p>
				First we put together our document. In our example it's a tweet. This tweet is going to be a <code>Elastica_Document</code> which is then added to our type tweet in the index twitter.
			</p>
<pre class="prettyprint">
// The Id of the document
$id = 1;

// Create a document
$tweet = array(
'id'      => $id,
'user'    => array(
'name'      => 'mewantcookie',
'fullName'  => 'Cookie Monster'
),
'msg'     => 'Me wish there were expression for cookies like there is for apples. "A cookie a day make the doctor diagnose you with diabetes" not catchy.',
'tstamp'  => '1238081389',
'location'=> '41.12,-71.34',
'_boost'  => 1.0
);
$tweetDocument = new Elastica_Document($id, $tweet);

// Add tweet to type
$elasticaType->addDocument($tweetDocument);

// Refresh Index
$elasticaType->getIndex()->refresh();</pre>
			<p>
				Now the index contains a document. But that's not enough! Add more documents to the index, so a search makes sense!
			</p>

	<h2 id="section-search">Search documents</h2>
		<p>
			With some documents in the index we can start searching. A basic search is executing a query against elasticsearch. The result of a query then can be filtered by defining some filters. Additionally you can retrieve facets to create a search navigation based on filters. For further information check out the <a href="http://www.elasticsearch.org/guide/reference/query-dsl/">Query DSL</a> and the <a href="http://www.elasticsearch.org/guide/reference/api/search/facets/">Facets</a> reference. In this example we don't use all possible options to keep it simple. Take a look in the <a href="api/index.html">Elastica API</a> for more options.
		</p>

		<h3 id="section-query">Search query</h3>
			<p>
				A query retrieves a set of results matching a query. So if you search with a <code>match_all</code> query you would get all the documents in that index or type, depending on where you search. The way elasticsearch finds the documents depends also on which analyzers you defined when creating the index.
			</p>
			<p>
				So how do you define a query in Elastica? Just create a Query object and give it some data. It's a simple as that.
			</p>
<pre class="prettyprint">
// Define a Query. We want a string query.
$elasticaQueryString 	= new Elastica_Query_QueryString();
$elasticaQueryString->setDefaultOperator('AND');
$elasticaQueryString->setQuery('sesam street');

// Create the actual search object with some data.
$elasticaQuery 		= new Elastica_Query();
$elasticaQuery->setQuery($elasticaQueryString);
$elasticaQuery->setFrom(1);
$elasticaQuery->setLimit(4);

//Search on the index.
$elasticaResultSet 	= $elasticaIndex->search($elasticaQuery);</pre>

			<p>I know, at first it seems a bit complicated with the nesting of all the arrays. But it really isn't. The <code>$elasticaQuery</code> contains all the information regarding our query. We will add filter and facets later. Your search can always consist of only one query but as-much-as-you-like filters and facets.</p>

		<h3 id="section-retrieve">Retrieve results</h3>
			<p>In the <code>$elasticaResultSet</code> all the resulting documents are stored. You can get them together with some statistics easily.</p>
<pre class="prettyprint">
$elasticaResults 	= $elasticaResultSet->getResults();
$totalResults 		= $elasticaResultSet->getTotalHits();

foreach ($elasticaResults as $elasticaResult) {
var_dump($elasticaResult->getData());
}</pre>

		<h3 id="section-filter">Add filter</h3>
			<p>
				Adding and nesting filter can get a bit complex but I try to keep it simple. Filters are added to the search query object (<code>$elasticaQuery</code>). Let's assume we already have a query searching for sesam street. We now can define some filters on the resulting data. So if we want to find all muppets that like cookies and are either blue or green, our filters would look something like that:
			</p>
<pre class="prettyprint">
// Filter for being of color blue
$elasticaFilterColorBlue	= new Elastica_Filter_Term();
$elasticaFilterColorBlue->addTerm('color', 'blue');

// Filter for being of color green
$elasticaFilterColorGreen	= new Elastica_Filter_Term();
$elasticaFilterColorGreen->addTerm('color', 'green');

// Filter for liking cookies
$elasticaFilterLikesCookies	= new Elastica_Filter_Term();
$elasticaFilterLikesCookies->addTerm('likes', 'cookies');

// Filter 'or' for the color, adding the color filters
$elasticaFilterOr 	= new Elastica_Filter_Or();
$elasticaFilterOr->addFilter($elasticaFilterColorBlue);
$elasticaFilterOr->addFilter($elasticaFilterColorGreen);

// Filter 'and' for the colors and likes
$elasticaFilterAnd 	= new Elastica_Filter_And();
$elasticaFilterAnd->addFilter($elasticaFilterOr);
$elasticaFilterAnd->addFilter($elasticaFilterLikesCookies);

// Add filter to the search object.
$elasticaQuery->setFilter($elasticaFilterAnd);</pre>

			<p>
				With the filters added, the search is ready to be executed. Probably this would return the cookie monster. But that depends on the data you added to your index, of course.
			</p>

		<h3 id="section-facets">Facets</h3>
			<p>
				A facet returns some information on the data in your database, depending on your search query. So let's say there is a field <code>tag</code> in our documents. In the results of our query we want to know, which tags are used by the results and how many documents have a certain tag. Read more in the <a href="http://www.elasticsearch.org/guide/reference/api/search/facets/">facets API</a>.
			</p>
			<p>
				Here is a quick example on how to define a facet for your query:
			</p>
<pre class="prettyprint">
// Define a new facet.
$elasticaFacet 	= new Elastica_Facet_Terms('myFacetName');
$elasticaFacet->setField('tags');
$elasticaFacet->setSize(10);
$elasticaFacet->setOrder('reverse_count');

// Add that facet to the search query object.
$elasticaQuery->addFacet($elasticaFacet);</pre>

			<p>
				This will return 10 tags together with a count of the documents in your current filtered search query. Retrieving the facets works similar to the retrieval of the documents.
			</p>
<pre class="prettyprint">
// Get facets from the result of the search query
$elasticaFacets = $elasticaResultSet->getFacets();

// Notice, that myFacetName is the same as above
// when the facet was defined.
foreach ($elasticaFacets['myFacetName']['terms'] as $elasticaFacet) {
var_dump($elasticaFacet);
}</pre>


	<h2 id="section-credits">Credits</h2>
		<p>
			Credits go to <a href="https://github.com/ruflin/Elastica/network/members">all users that gave feedback and committed code</a>.
		</p>



