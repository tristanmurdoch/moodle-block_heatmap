M.block_heatmap = {
    config: 0,
    min: 0,
    max: 0,
    diff: 0,
    colourScale: new Array('#F5ECCE', '#F5D0A9', '#F5BCA9', '#F5A9A9', '#F78181'),
    numColours: 5,
    toggleState: true,

    initHeatmap: function (YUIObject, config, min, max, toggledon) {
        this.config = JSON.parse(config);
        this.min = min;
        this.max = max;
        this.diff = max - min;
        this.toggleState = toggledon == 1;
        if (this.toggleState) {
            this.showHeatmap();
        }
    },

    showHeatmap: function () {
        var module;
        var weight;
        var info;
        for (var i = 0; i < this.config.length; i++) {
            module = document.getElementById('module-' + this.config[i].cmid);
            weight = parseInt(this.config[i].numviews / this.diff * this.numColours - 1);
            if (module) {
                module.style.backgroundColor = this.colourScale[weight];
                info = '<div class="view_count">';
                info += M.str.block_heatmap.views;
                info += '&nbsp;';
                info += this.config[i].numviews;
                info += ', &nbsp;';
                info += M.str.block_heatmap.distinctusers;
                info += '&nbsp;';
                info += this.config[i].distinctusers;
                info += '</div>';
                module.innerHTML = module.innerHTML + info;
            }
        }
    },

    hideHeatmap: function () {
        for (var i = 0; i < this.config.length; i++) {
            module = document.getElementById('module-' + this.config[i].cmid);
            if (module) {
                module.style.backgroundColor = '';
                module.removeChild(module.getElementsByClassName('view_count')[0]);
            }
        }
    },

    toggleHeatmap: function () {
        this.toggleState = !this.toggleState;
        if(this.toggleState) {
            this.showHeatmap();
        }
        else {
            this.hideHeatmap();
        }
        M.util.set_user_preference('heatmaptogglestate', this.toggleState);
    }
};