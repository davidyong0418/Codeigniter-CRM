
<div class="analysis-of-website card">
      <div class="card-header">
         <h3>ANALYSIS OF YOUR WEBSITE</h3>
      </div>
      <?php 
        $subcontent= $this->db->where('template','website_analysis')->get('tbl_proposal_template')->result();
        print_r($subcontent[0]->content);
      ?>
      <?php if(!empty($response)){?>
      <div class="card-body" id="techinal_assessment">
         <h4 class="custom-color">TECHNICAL ASSESSMENT</h4>
         <p class="card-tex">
            There are many things your website is doing well, however the areas below should be improved if SEO (Search Engine Optimization) is a point of concern.  Your web developer should be able to correct most of these with relative ease.
         </p>
         <ol>
               <?php 
                  if($response->ruleGroups->SPEED->score < 85){
               ?>
            <li>
               <strong>PAGE LOAD SPEED</strong>
               <span>-</span>
               The page load speed not only affects the user experience, but Google has specifically declared that it would penalize a website ranking based on page load ⦁	speed, user experience, and ⦁	mobile⦁	 ⦁	friendliness (you can click on any words to see Google’s official declarations).  Your overall speed, user experience, and mobile friendliness are all poor in Google’s eyes.  The scores below are provided by Google, and a link to official page is included so you can verify it.
               <ul>
                  <li>Desktop Speed Score - 50 out of 100 <a href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo $response->id;?>">(according to Google Page Speed Insights(link))</a></li>
                  <li>Mobile Speed Score - 41 out of 100 <a href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo $response->id;?>">(according to Google Page Speed Insights(link))</a></li>
                  <li>Mobile User Experience - 65 out of 100 <a href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo $response->id;?>">(according to Google Page Speed Insights(link))</a></li>
                  <li>Failed Google’s mobile friendly test  <a href="https://search.google.com/test/mobile-friendly?url=<?php echo $response->id;?>">(according to Google Mobile Friendly Test(link))</a></li>
               </ul>
            </li>
            <?php }?>
            <li>
               <strong>NO MINIFICATION OF JS & CSS</strong>
               <span>-</span>
               Minifying these files can help increase your page load speed. <a href="https://search.google.com/test/mobile-friendly?url=<?php echo $response->id;?>">(verified by Google Page Speed Insights(link)</a>
            </li>
            <li>
               <strong> PAGE CACHING NOT LEVERAGED</strong>
               <span>-</span>
               Your website is not leveraging browser caching.  Caching your pages will decrease their loading time by storing them temporarily on the user’s device. The caching delay has to be set depending on the page updating frequency.  Since your website content rarely changes, page caching is highly recommended. <a href="https://search.google.com/test/mobile-friendly?url=<?php echo $response->id;?>">(verified by Google Page Speed Insights (link))</a>
            </li>
            <li>
               <strong>PAGE CAXHING NOT ALLOWED</strong>
               <span>-</span>
               Your website does not allow page caching.  Caching your pages will decrease their loading time by storing them temporarily on the user’s device. The caching delay has to be set depending on the page updating frequency.  Since your website content rarely changes, page caching is highly recommended. <a href="https://search.google.com/test/mobile-friendly?url=<?php echo $response->id;?>">(verified by Google Page Speed Insights (link))</a>
               
            </li>
            <li>
               <strong>HEAVY IMAGES </strong>
               <span>-</span>
               Heavy images (big file size) lower your page load time and the page load grade.  Your web page contains <?php ?> heavy images <a href="https://search.google.com/test/mobile-friendly?url=<?php echo $response->id;?>">(verified by Google Page Speed Insights (link))</a><?php echo $response->formattedResults->ruleResults->OptimizeImages->urlBlocks[0]->urls[0]->result->args[0]->value;?>
            </li>
            <li>
               <strong>EXTREME NUMBER OF WEBSITES ON SAME SERVER & IP </strong>
               <span>-</span>
               There are 300 other sites hosted on this server. If any one site on the server is compromised, it opens a gateway for the attacker to gain access to the other sites hosted on the same server as well. The higher the number of sites on a server the higher the risk.  There is also great SEO benefit in a low-share or dedicated server.
            </li>
            <li>
               <strong>MANY W3C ERRORS</strong>
               <span>-</span>
               An HTML document must be valid to be fully compatible and readable by browsers and robots, yours has 59 errors.
            </li>
            <li>
               <strong>TEXT TO CODE RATIO IS TOO LOW </strong>
               <span>-</span>
               Only 0.05% of your page content is text.  The text/code ratio is used to determine the quality of a page. The higher the ratio is, the more important the content will be. Acceptable ranges are between 25% - 75%.
            </li>
            <li>
               <strong>USE OF FLASH</strong>
               <span>-</span>
               While it often looks nicer, Flash content can't be properly indexed by search engines and challenges your SEO efforts.
            </li>
            <li>
               <strong>NO 404 PAGE</strong>
               <span>-</span>
               404 error pages are displayed when the page you are trying to open can't be found on the site's server for a variety of reasons.  If someone visits a page on your website that doesn’t exist, they should be redirected to the home page or a 404 page that provides them useful information.
            </li>
            <li>
               <strong>PRINTING STYLE SHEET IS MISSING </strong>
               <span>-</span>
               A dedicated style sheet for printing allows users to print the content of your website in a more readable way. It is especially important if you are creating content that you want your website visitors to consume, such as blog articles and ‘how-to’ articles.  Even if your user may never print anything, having the style sheet is viewed favorably by the search engines.
            </li>
            <li>
               <strong>NO HTML COMPRESSION </strong>
               <span>-</span>
               You should compress your HTML to reduce your page size and page loading times - this will help your site retain visitors and increase page views. If you were using compression, you could be compressing your HTML size by 63 % - from 15.04 Kb to 5.52 Kb which would further reduce your page loading time.
            </li>
            <li>
               <strong>DEPRECATED HTML ELEMENTS</strong>
               <span>-</span>
               A deprecated element is one that has been outdated by newer constructs. Deprecated elements may become obsolete in future versions of HTML, so it is highly recommended not to use them.
            </li>
            <li>
               <strong>NOT USING SCHEMA</strong>
               <span>-</span>
               Schema defines specific business-related data to the search engines, not in use here.
            </li>
            <li>
               <strong>NO OPEN GRAPH </strong>
               <span>-</span>
               Open Graph is a term created by Facebook developers to describe the ability for some social networking software to interact across different platforms. This allows different websites and applications to share information about a user, their interests and even their friendship network.
            </li>
            <li>
               <strong>NO USE OF HEADING TAGS </strong>
               <span>-</span>
               Heading Tags are used in order to provide both your visitors and the search engine with a clear view about the content’s importance.
            </li>
            <li>
               <strong>MANY IMAGES NOT USING ALT TAGS </strong>
               <span>-</span>
               Alternative text describes your images so they can appear in image search results, be cached by the search engine bots (they cannot read images), and be read to blind users.
            </li>
            <li>
               <strong>CONFIGURE IMAGE ALT TEXT</strong>
               <span>-</span>
               You have several images without alt attributes. The alt attribute of an image element informs the search engine of its description. Choose your keywords carefully to be indexed in Image SERPs. The length of the alternative text should be more than 80 characters. This attribute is also used by assistance software to describe images for blind people.
            </li>
            <li>
               <strong>UNTITLED PAGE </strong>
               <span>-</span>
               Page titles appear in search results and at the top of the browser's window when visiting the site. Appropriate page titles are particularly important for search engine optimization.
            </li>
            <li>
               <strong>IMAGE ALT TAGS MISSING </strong>
               <span>-</span>
               Alternative text (the alt attribute) is missing for 1 of 4 images we found on your website <a href="<?php echo $response->id;?>"> <?php echo $response->id;?></a>.  Search engine crawlers cannot actually "see" images, so the alternative text attribute allows you to assign a specific description to each image. They are also used by screen readers to help provide some context for the visually impaired.
            </li>
            <li>
               <strong>TITLE TAG NOT OPTIMIZED </strong>
               <span>-</span>
               Your title tag is not well optimized for any service related keywords.
            </li>
            <li>
               <strong>META TAG DESCRIPTION MISSING</strong>
               <span>-</span>
               The meta-description tag is missing from your page. You should include this tag in order to provide a brief description of your page which can be used by search engines or directories.
            </li>
            <li>
               <strong>NO KEYWORDS</strong>
               <span>-</span>
               There are not meta keywords defined for any of your website's pages.
            </li>
            <li>
               <strong>TOO MANY LINKED CSS FILES (18)</strong>
               <span>-</span>
               When possible it is recommended to combine your CSS files in one single file (or close) to lower the number of HTTP requests.
            </li>
            <li>
               <strong>TOO MANY SCRIPTS IN THE FOOTER (40)</strong>
               <span>-</span>
               You have 40 scripts at the footer of your website. This can negatively impact the loading time of your site and it creates significant hacking vulnerabilities. This is often an indicator of having too many plugins (like apps for your website) which can cause a litany of problems such as website crashes, poor website performance, and security breaches.
            </li>
            <li>
               <strong>META ROBOTS.TXT IS MISSING</strong>
               <span>-</span>
               When missing, search engines index every page of your website (not desired and not safe).
               <a href="http://www.google.com/webmasters/tools/<?php echo $response->id;?>"></a>
            </li>
            <li>
               <strong>SITEMAP NOT XML</strong>
               <span>-</span>
               you have a sitemap link, but it is not an XML Sitemap which helps search engines know about all the pages on your site that you want them to know about.
               <a href="http://www.google.com/webmasters/tools/<?php echo $response->id;?>">(verified by viewing sitemap link)</a>
            </li>
            <li>
               <strong>⦁	NEED A SITEMAP </strong>
               <span>-</span>
               A sitemap is a file containing an ordered organization of the linking structure of your website. This file is not only used to define the importance you attach to each page, but also to help the search engines crawl your entire website. The location of the sitemap must be defined in the robots.txt file.
               <a href="https://www.google.com/webmasters/tools/robots-testing-tool/<?php echo $response->id;?>">(verified by Google Robot.txt Tester (link))</a>
            </li>
            <li>
               <strong>INTERNAL LINKS NOT SEO FRIENDLY</strong>
               <span>-</span>
               Links on your website have not been optimized for search engines.  Links such as 27 place key words in the URL that do not correspond to your service keywords and the link structure pushes the beneficial keywords (limo buses) further from the domain name.
            </li>
            <li>
               <strong>NO FAVICON</strong>
               <span>-</span>
               The Favicon is a small icon associated with a website. The Favicon is important because it is displayed next to the website's URL in the address bar of the browser as well as in bookmarks and shortcuts.
            </li>
            <li>
               <strong>NO ANALYTICS INSTALLED</strong>
               <span>-</span>
               Web analytics let you measure visitor activity on your website. You should have at least one analytics tool installed, but It can also be good to install a second in order to cross-check the data.  Popular Analytics Tools Include: Google Analytics, & Quantcast.
            </li>
         </ol>
      </div>
      <?php }?>
      
</div>
<script>
var today = "<?php echo date('Y-m-d');?>";
var website = "<?php if(!empty($response)){echo $response->id;}else{'No recommended website';}?>";
$(document).ready(function(){
      $('#website_analysis .proposal-content').appendTo('#techinal_assessment');
      $('#website_analysis .today_date').html(today);
      $('#website_analysis .media_add_site').html(website);
});
</script>