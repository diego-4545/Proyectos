#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>

#define BIGM 1e6
#define ME 1e-8

void imprimir_valores(int m, int n, int cantS, int cantA, int columnas, double tabla[][columnas], char base[][16], int paso){
    double vars[n + cantS + cantA];
    for(int j=0;j<n + cantS + cantA;j++) vars[j] = 0.0;

    for(int i=1;i<=m;i++){
        char *nombre = base[i-1];
        int k;
        if(nombre[0]=='x'){ k = atoi(nombre+1)-1; vars[k] = tabla[i][columnas-1]; }
        else if(nombre[0]=='s'){ k = n + atoi(nombre+1)-1; vars[k] = tabla[i][columnas-1]; }
        else if(nombre[0]=='a'){ k = n + cantS + atoi(nombre+1)-1; vars[k] = tabla[i][columnas-1]; }
    }

    for(int j=0;j<n;j++) printf("x%d = %.6f  ", j+1, vars[j]);
    for(int j=0;j<cantS;j++) printf("s%d = %.6f  ", j+1, vars[n+j]);
    for(int j=0;j<cantA;j++) printf("a%d = %.6f  ", j+1, vars[n+cantS+j]);
    printf("z = %.6f\n", tabla[0][columnas-1]);
}

void actualizar_columnas_basicas(int *colBasica, char base[][16], int m, int n, int cantS, int cantA, int columnas) {
    for (int j = 0; j < columnas; ++j) colBasica[j] = 0;
    for (int i = 1; i <= m; ++i) {
        char *bn = base[i-1];
        if (bn[0] == 'x') {
            int k = atoi(bn + 1);
            if (k >= 1 && k <= n) colBasica[1 + (k-1)] = 1;
        } else if (bn[0] == 's') {
            int k = atoi(bn + 1);
            if (k >= 1 && k <= cantS) colBasica[1 + n + (k-1)] = 1;
        } else if (bn[0] == 'a') {
            int k = atoi(bn + 1);
            if (k >= 1 && k <= cantA) colBasica[1 + n + cantS + (k-1)] = 1;
        }
    }
}

void detectar_solucion_multiple(int columnas, double tabla[][columnas], int m, int n, int cantS, int cantA, int *colBasica) {
    int encontrado = 0;
    for (int j = 1; j < columnas - 1; ++j) {
        if (colBasica[j] == 0) {
            double rc = tabla[0][j];
            if (fabs(rc) < ME) {
                if (j >= 1 && j <= n) {
                    printf("Variable x%d (no basica) tiene costo reducido de 0, hay solucion multiple.\n", j);
                    encontrado = 1;
                } 
            }
        }
    }
}

int main() {
    int m = 2;
    int n = 2;

    double A[2][2] = {
        {2, 1},
        {3, 4}
    };
    double b[2] = {2, 12};
    char signo[2][3] = {"<=", ">="};
    double c[2] = {3, 2};
    
    int cantS = 0, cantA = 0;
    for (int i = 0; i < m; ++i) {
        if (strcmp(signo[i], "<=") == 0) cantS++;
        else if (strcmp(signo[i], ">=") == 0) { cantS++; cantA++; }
        else if (strcmp(signo[i], "=") == 0) cantA++;
    }

    int columnas = 1 + n + cantS + cantA + 1;
    int filas = m + 1;

    double tabla[filas][columnas];
    for(int i=0;i<filas;i++)
        for(int j=0;j<columnas;j++)
            tabla[i][j] = 0.0;

    tabla[0][0] = 1.0;

    int idxS = 0, idxA = 0;
    char base[m][16];
    for(int i=1;i<=m;i++){
        for(int j=0;j<n;j++) tabla[i][1+j] = A[i-1][j];

        if(strcmp(signo[i-1], "<=")==0){
            tabla[i][1+n+idxS] = 1.0;
            snprintf(base[i-1],16,"s%d",idxS+1);
            idxS++;
        } else if(strcmp(signo[i-1], ">=")==0){
            tabla[i][1+n+idxS] = -1.0;
            idxS++;
            tabla[i][1+n+cantS+idxA] = 1.0;
            snprintf(base[i-1],16,"a%d",idxA+1);
            idxA++;
        } else {
            tabla[i][1+n+cantS+idxA] = 1.0;
            snprintf(base[i-1],16,"a%d",idxA+1);
            idxA++;
        }

        tabla[i][columnas-1] = b[i-1];
    }

    for(int j=0;j<n;j++) tabla[0][1+j] = -c[j];
    tabla[0][columnas-1] = 0.0;

    for(int i=0;i<m;i++){
        if(base[i][0]=='a'){
            tabla[i-1][1+n+cantS+i-1]=BIGM;
            for(int j=1;j<columnas;j++) tabla[0][j]-=BIGM*tabla[i+1][j];
        }
    }

    int colBasica[columnas];
    actualizar_columnas_basicas(colBasica,base,m,n,cantS,cantA,columnas);

    const int W_LABEL=17,W_COL=17,W_R=17,W_PC=17,W_BASE=12;
    int paso=0;

    while(1){
        int columnaEntrante=-1;
        double mejorValor=0.0;
        for(int j=1;j<columnas-1;j++){
            double val = tabla[0][j];
            if(val < -ME && val < mejorValor){mejorValor=val; columnaEntrante=j;}
        }

        double pruebaCociente[m];
        for(int i=0;i<m;i++) pruebaCociente[i]=INFINITY;
        int filaPivote=-1;
        double mejorPc=INFINITY;

        if(columnaEntrante!=-1){
            for(int i=1;i<=m;i++){
                double aij=tabla[i][columnaEntrante];
                double rhs=tabla[i][columnas-1];
                if(aij>ME){
                    double r=rhs/aij;
                    pruebaCociente[i-1]=r;
                    if(r<mejorPc){mejorPc=r; filaPivote=i;}
                }
            }
        }

        printf("\n--- Paso %d ---\n",paso);
        printf("%-*s",W_LABEL,"Fila");
        printf("%*s",W_COL,"z");
        for(int j=1;j<=n;j++){char buf[16]; snprintf(buf,16,"x%d",j); printf("%*s",W_COL,buf);}
        for(int j=1;j<=cantS;j++){char buf[16]; snprintf(buf,16,"s%d",j); printf("%*s",W_COL,buf);}
        for(int j=1;j<=cantA;j++){char buf[16]; snprintf(buf,16,"a%d",j); printf("%*s",W_COL,buf);}
        printf("%*s%*s%*s\n",W_R,"R",W_PC,"Cociente",W_BASE,"Base");

        printf("%-*s",W_LABEL,"Z");
        printf("%*.*f",W_COL,4,tabla[0][0]);
        for(int j=1;j<columnas-1;j++) printf("%*.*f",W_COL,4,tabla[0][j]);
        printf("%*.*f",W_R,4,tabla[0][columnas-1]);
        printf("%*s%*s\n",W_PC,"-",W_BASE,"-");

        for(int i=1;i<=m;i++){
            printf("%-*s",W_LABEL,base[i-1]);
            printf("%*.*f",W_COL,4,tabla[i][0]);
            for(int j=1;j<columnas-1;j++) printf("%*.*f",W_COL,4,tabla[i][j]);
            printf("%*.*f",W_R,4,tabla[i][columnas-1]);
            if(columnaEntrante!=-1){
                if(isfinite(pruebaCociente[i-1]))
                    printf("%*.*f",W_PC,4,pruebaCociente[i-1]);
                else
                    printf("%*s",W_PC,"-");
            }
            printf("%*s\n",W_BASE,base[i-1]);
        }
        
        imprimir_valores(m, n, cantS, cantA, columnas, tabla, base, paso);

        detectar_solucion_multiple(columnas,tabla,m,n,cantS,cantA,colBasica);

        if(columnaEntrante==-1){
            printf("\nYa no hay más coeficientes negativos en Z, se llegó a la solución óptima.\n");
            break;
        }

        if(filaPivote==-1){
            printf("\nEl problema es no acotado, la columna entrante no tiene coeficientes mayores a 0.\n");
            return 1;
        }

        double pivote = tabla[filaPivote][columnaEntrante];
        for(int j=1;j<columnas;j++) tabla[filaPivote][j]/=pivote;

        for(int i=0;i<filas;i++){
            if(i==filaPivote) continue;
            double factor=tabla[i][columnaEntrante];
            if(fabs(factor)<ME) continue;
            for(int j=1;j<columnas;j++) tabla[i][j]-=factor*tabla[filaPivote][j];
        }

        if(columnaEntrante>=1 && columnaEntrante<=n) snprintf(base[filaPivote-1],16,"x%d",columnaEntrante);
        else if(columnaEntrante>n && columnaEntrante<=n+cantS) snprintf(base[filaPivote-1],16,"s%d",columnaEntrante-n);
        else snprintf(base[filaPivote-1],16,"a%d",columnaEntrante-n-cantS);

        actualizar_columnas_basicas(colBasica,base,m,n,cantS,cantA,columnas);
        

        paso++;
    }

    int infactible=0;
    for(int i=1;i<=m;i++){
        if(base[i-1][0]=='a'){
            double rhs=tabla[i][columnas-1];
            if(rhs>ME){
                infactible=1;
                printf("\nEl problema no tiene solucion porque quedó la variable artificial %s con b = %.8f > 0\n",base[i-1],rhs);
            }
        }
    }

    if(!infactible){
        printf("\n=== Resultado final ===\n");
        imprimir_valores(m, n, cantS, cantA, columnas, tabla, base, paso);
    } 
}


