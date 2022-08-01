pipeline {
    agent any

    stages {

        stage('Get Source') {
            steps {
                git url: 'https://github.com/accerq/i-educar.git', branch: 'main'
            }
        }

        stage('Docker Build') {
            steps {
                script {
                    dockerapp = docker.build("accerq/i-educar:${env.BUILD_ID}"),
                        '-f docker/php/Dockerfile .'
                    dockerapp = docker.build("accerq/i-educar:${env.BUILD_ID}"),
                        '-f docker/nginx/Dockerfile .'
                }
            }
        }

        stage('Docker Push Image') {
            steps {
                script {
                    docker.withRegistry('https://registry.hub.docker.com', 'dockerhub') {
                    dockerapp.push('latest')
                    dockerapp.push("${COMMIT}")
                    }
                }
            }
        }
    }
}