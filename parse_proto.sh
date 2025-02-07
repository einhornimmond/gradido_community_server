#!/bin/bash
# Copyright 2015 gRPC authors.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
# need grpc only for hedera hashgraph
#PHP_PLUGIN="$(which grpc_php_plugin)"
#protoc --proto_path=./src/protobuf/gradido  --php_out=./src/  --grpc_out=./src/ --plugin=protoc-gen-grpc=$PHP_PLUGIN ./src/protobuf/gradido/*.proto
PROTO_BIN=../login_server/build/dependencies/protobuf/cmake/bin/protoc

${PROTO_BIN} --proto_path=./src/protobuf  --php_out=./src/Model/Messages  ./src/protobuf/gradido/*.proto

