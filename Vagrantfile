# -*- mode: ruby -*-
# vi: set ft=ruby :

# Usage: ENV=staging vagrant up
environment = "development"
if ENV["ENV"] && ENV["ENV"] != ''
    environment = ENV["ENV"].downcase
end

role = ""
if ENV["ROLE"] && ENV["ROLE"] != ''
    role = ENV["ROLE"].downcase
end

Vagrant.configure("2") do |config|

	if environment == 'development'
	  # All Vagrant configuration is done here. The most common configuration
	  # options are documented and commented below. For a complete reference,
	  # please see the online documentation at vagrantup.com.
	  # Every Vagrant virtual environment requires a box to build off of.
	  config.vm.box = "precise64"
	
	  # The url from where the 'config.vm.box' box will be fetched if it
	  # doesn't already exist on the user's system.
	  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
	
	  # Create a forwarded port mapping which allows access to a specific port
	  # within the machine from a port on the host machine. In the example below,
	  # accessing "localhost:8080" will access port 80 on the guest machine.
	  config.vm.network :forwarded_port, guest: 80,    host: 10080    # apache http
	  config.vm.network :forwarded_port, guest: 3306,  host: 3306  # mysql
	  config.vm.network :forwarded_port, guest: 10081, host: 10081 # zend http
	  config.vm.network :forwarded_port, guest: 10082, host: 10082 # zend https
	  config.vm.network :forwarded_port, guest: 27017, host: 27017 # mongodb
	
	  config.vm.network :private_network, ip: "192.168.42.69"
	
	        config.vm.provider :virtualbox do |vb, override|
	            # Boot with headless mode
	            vb.gui = false
	
	            # Use VBoxManage to customize the VM. For example to change memory to 512:
	            vb.customize ["modifyvm", :id, "--memory", 512]
	
	            # Enable symbolic link creation in VirtualBox
	            vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/vagrant-root", "1"]
	
	            # Use NFS for shared project directory (ignored on Windows)
	            # config.vm.synced_folder ".", "/vagrant", :nfs => true
	
	            # Set permissions for shared directory
	            config.vm.synced_folder ".", "/vagrant", :group => "www-data"
	        end
	
	        config.vm.provision :puppet do |puppet|
	            # Enable provisioning with Puppet stand alone.  Puppet manifests
	            # are contained in a directory path relative to this Vagrantfile.
	            # You will need to create the manifests directory and a manifest in
	            # the file base.pp in the manifests_path directory.
	            puppet.options        = "--verbose --debug"
	            puppet.manifests_path = "puppet/manifests"
	            puppet.module_path    = "puppet/modules"
	            puppet.manifest_file  = "site.pp"
	            puppet.facter         = {
	                "vagrant"     => true,
	                "environment" => environment,
	                "role"        => "local",
	            }
	        end
	end
	
	if environment == 'staging'
	
	    config.vm.provision :shell, :path => "aws_bootstrap.sh"
	    
        # Every Vagrant virtual environment requires a box to build off of.
        config.vm.box = "dummy"

        # Disable automatic syncing of project directory
        # config.vm.synced_folder ".", "/vagrant", disabled: true

        # Amazon Web Services
        config.vm.provider :aws do |aws, override|
            aws.access_key_id     = "AKIAIG4GFINOWTIQHL7A"
            aws.secret_access_key = "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"
            aws.instance_type     = "t1.micro"
            aws.region            = "us-east-1"
            aws.security_groups   = [ ]
            aws.tags              = {
                "vagrant"     => "true",
                "environment" => environment,
                "role"        => role,
                "elastic_ip"  => "54.197.236.109",
                "Name"        => "GoogleGlass"
            }

            # us-east-1
            aws.region_config "us-east-1" do |region|
                region.ami          = "ami-d0f89fb9"
                region.keypair_name = "googleglass"
            end

            override.ssh.username         = "ubuntu"
            override.ssh.private_key_path = "~/.ssh/googleglass.pem"
        end

        # Puppet
        config.vm.provision :puppet do |puppet|
            puppet.options        = "--verbose --debug"
            puppet.manifests_path = "puppet/manifests"
            puppet.module_path    = "puppet/modules"
            puppet.manifest_file  = "site.pp"
            puppet.facter         = {
                "vagrant"     => true,
                "environment" => environment,
                "role"        => role,
            }
        end
    end
end
