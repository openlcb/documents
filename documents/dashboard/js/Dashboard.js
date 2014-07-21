var m_names = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
function dateToString(timestamp) {
  d = new Date(timestamp * 1000);
  var curr_date = d.getDate();
  var curr_month = d.getMonth();
  var curr_year = d.getFullYear();
  return m_names[curr_month] + " " + curr_date + " " + curr_year;
}

var Dashboard = new function () {
  var self = this;

  this.specifications = {};
  this.specifications.categories = [];

  this.setTemplate = function (template) {
    if (template) {
      self.template = template;
    }
  };

  this.loadSpecificationDoc = function (origin) {
    var url = origin+".json";
    $.ajax(url).then(
      function(data, textStatus, jqXHR) {
        //success
        data = JSON.parse(data);

        //find the category for this doc, and insert it
        for (var i = self.specifications.categories.length - 1; i >= 0; i--) {
          if(self.specifications.categories[i].name == data.category) {
            self.specifications.categories[i].docs.unshift(data);
            break;
          }
        }
      },
      function(jqXHR, textStatus, errorThrown) {
        //failure
        console.log("Failed to load "+url);
        console.log(jqXHR);
      }
    );
  };

  this.loadSpecifications = function () {
    var url = "specifications.json";
    $.ajax(url).then(
      function(data, textStatus, jqXHR) {
        //success
        data = JSON.parse(data);
        for (var i = data.categories.length - 1; i >= 0; i--) {
          self.specifications.categories.unshift({
            name: data.categories[i],
            docs: []
          });
        }

        for(i = data.docs.length - 1; i >= 0; i--) {
          self.loadSpecificationDoc(data.docs[i]);
        }
      },
      function(jqXHR, textStatus, errorThrown) {
        //failure
        console.log(jqXHR);
      }
    );
  };

  this.loadHardware = function() {

  };

  this.loadSoftware = function() {
    
  };

  this.Initialize = function () {
    $.ajaxSetup({ cache: false });
    
    self.ra = new Ractive({
      el: "Dashboard-container",
      template: self.template,
      data: {
        specifications: self.specifications,
        showSpecifications: true,
        showHardware: false,
        showSoftware: false,
        ResourceManager: ResourceManager,
        dateToString: dateToString
      }
    });


    //Load JSON data
    self.loadSpecifications();
    self.loadHardware();
    self.loadSoftware();

    self.ra.on('onSpecificationsTabClicked', function(event) {
      self.ra.set('showHardware', false);
      self.ra.set('showSoftware', false);
      self.ra.set('showSpecifications', true);
    });
    self.ra.on('onHardwareTabClicked', function(event) {
      self.ra.set('showSpecifications', false);
      self.ra.set('showSoftware', false);
      self.ra.set('showHardware', true);
    });
    self.ra.on('onSoftwareTabClicked', function(event) {
      self.ra.set('showSpecifications', false);
      self.ra.set('showHardware', false);
      self.ra.set('showSoftware', true);
    });
  };
};
