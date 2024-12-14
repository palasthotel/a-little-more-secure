{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.php82
    pkgs.php82Packages.composer
  ];
}
