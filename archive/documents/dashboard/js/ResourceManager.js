var ResourceManager = new function() {
  var self = this;

  this.timeout = 50000;

  this.pendingLoads = 0;

  this.templates = [];
  this.objects = [];

  this.Initialize = function(templates) {
    for (var i = 0; i < templates.length; i++) {
      // console.log("loading template " + templates[i]);
      self.templates.push(templates[i]);
      self.loadTemplate(templates[i]);
    }
  };

  this.loadTemplate = function(id) {
    self.pendingLoads++;
    this.url = "templates/" + id + ".html";
    $.ajax(this.url).then(self.loadTemplateSuccess, self.loadTemplateFailed);
  };

  this.loadTemplateSuccess = function (data, textStatus, jqXHR) {
    // console.log("loaded template from " + this.url);
    var id = this.url.substring(this.url.lastIndexOf("/")+1).replace(".html","");
    var obj = eval(id);
    self.objects[id] = obj;
    obj.setTemplate(jqXHR.responseText);
    self.pendingLoads--;
    self.signalWhenReady();
  };

  this.loadTemplateFailed = function (jqXHR, textStatus, errorThrown) {
    // console.log("couldn't load template from " + this.url + ", error: " + errorThrown);
    self.pendingLoads--;
  };

  this.signalWhenReady = function() {
    if(self.pendingLoads === 0) {
      for (var i = 0; i < self.templates.length; i++) {
        // console.log("Initializing " + self.templates[i])
        self.objects[self.templates[i]].Initialize();
      }
    }
  };
}
