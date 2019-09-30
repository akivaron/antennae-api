#include <iostream>
#include <stdlib.h>
using namespace std;

int main(){
        string nama;
    int nis, nilai_akhir;
    cout<<"===============================Aplikasi Input Nilai Siswa====================================";

    cout<<"\n||";

    cout<<"\n|| Masukkan Nama Siswa        : ";
    cin>>nama;

    cout<<"|| Masukkan NIS               : ";
    cin>>nis;

    cout<<"|| Masukkan Nilai Akhir Siswa : ";
    cin>>nilai_akhir;

    cout<<"||";

    if(nilai_akhir>=75){
        cout<<"\n|| Siswa bernama "<<nama<<" dan dengan nis "<<nis<<" mempunyai nilai akhir sebesar "<<nilai_akhir<<" dinyatakan LULUS";
    }
    else{
        cout<<"\n|| Siswa bernama "<<nama<<" dan dengan nis "<<nis<<" mempunyai nilai akhir sebesar "<<nilai_akhir<<" dinyatakan TIDAK LULUS";
    }

    cout<<"\n============================================================================================="<<endl;

    return 0;
}