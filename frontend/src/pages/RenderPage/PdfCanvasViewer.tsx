import { useEffect, useMemo, useRef, useState } from 'react'
import pdfWorkerSrc from 'pdfjs-dist/legacy/build/pdf.worker.min.mjs?url'
import styles from './RenderPage.module.css'

type PdfDocumentLike = {
  numPages: number
  getPage: (pageNumber: number) => Promise<PdfPageLike>
  destroy: () => Promise<void>
}

type PdfPageLike = {
  getViewport: (params: { scale: number }) => { width: number; height: number }
  render: (params: {
    canvasContext: CanvasRenderingContext2D
    viewport: { width: number; height: number }
  }) => PdfRenderTaskLike
}

type PdfRenderTaskLike = {
  promise: Promise<void>
  cancel?: () => void
}

type PdfLoadingTaskLike = {
  promise: Promise<PdfDocumentLike>
  destroy?: () => void
}

type PdfJsModuleLike = {
  getDocument: (source: Record<string, unknown>) => PdfLoadingTaskLike
  GlobalWorkerOptions: {
    workerSrc: string
  }
}

type PdfCanvasViewerProps = {
  fileData: Uint8Array
  fileName: string
  isDark: boolean
  initialPage?: number
  initialZoom?: number
  onProgressChange?: (progress: { page: number; zoom: number }) => void
}

const ZOOM_MIN = 0.6
const ZOOM_MAX = 2.4
const ZOOM_STEP = 0.2
const FITTED_PAGE_MAX_WIDTH = 1080

const clampZoom = (value: number) => {
  if (!Number.isFinite(value)) {
    return 1
  }

  return Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, Number(value.toFixed(2))))
}

const ensurePromiseWithResolvers = () => {
  if (typeof Promise.withResolvers === 'function') {
    return
  }

  Promise.withResolvers = function withResolvers<T>() {
    let resolve!: (value: T | PromiseLike<T>) => void
    let reject!: (reason?: unknown) => void

    const promise = new Promise<T>((resolvePromise, rejectPromise) => {
      resolve = resolvePromise
      reject = rejectPromise
    })

    return { promise, resolve, reject }
  }
}

let pdfJsModulePromise: Promise<PdfJsModuleLike> | null = null

const loadPdfJsModule = async () => {
  ensurePromiseWithResolvers()

  if (!pdfJsModulePromise) {
    pdfJsModulePromise = import('pdfjs-dist/legacy/build/pdf.mjs') as unknown as Promise<PdfJsModuleLike>
  }

  return pdfJsModulePromise
}

const isRenderCancelledError = (error: unknown) => {
  if (!(error instanceof Error)) {
    return false
  }

  return error.name === 'RenderingCancelledException' || error.name === 'AbortException'
}

export const PdfCanvasViewer = ({
  fileData,
  fileName,
  isDark,
  initialPage = 1,
  initialZoom = 1,
  onProgressChange,
}: PdfCanvasViewerProps) => {
  const containerRef = useRef<HTMLDivElement | null>(null)
  const canvasRef = useRef<HTMLCanvasElement | null>(null)
  const documentRef = useRef<PdfDocumentLike | null>(null)

  const [containerWidth, setContainerWidth] = useState(0)
  const [isDocumentLoading, setIsDocumentLoading] = useState(true)
  const [isPageRendering, setIsPageRendering] = useState(false)
  const [documentError, setDocumentError] = useState('')
  const [renderError, setRenderError] = useState('')
  const [totalPages, setTotalPages] = useState(0)
  const [currentPage, setCurrentPage] = useState(Math.max(1, Math.trunc(initialPage)))
  const [pageInput, setPageInput] = useState(String(Math.max(1, Math.trunc(initialPage))))
  const [zoom, setZoom] = useState(clampZoom(initialZoom))

  useEffect(() => {
    const element = containerRef.current

    if (!element) {
      return
    }

    const updateWidth = () => {
      setContainerWidth(Math.max(0, Math.floor(element.clientWidth)))
    }

    updateWidth()

    if (typeof ResizeObserver !== 'undefined') {
      const observer = new ResizeObserver((entries) => {
        const entry = entries[0]

        if (!entry) {
          return
        }

        setContainerWidth(Math.max(0, Math.floor(entry.contentRect.width)))
      })

      observer.observe(element)
      return () => observer.disconnect()
    }

    window.addEventListener('resize', updateWidth)
    return () => window.removeEventListener('resize', updateWidth)
  }, [])

  useEffect(() => {
    let isActive = true
    let loadingTask: PdfLoadingTaskLike | null = null
    let loadedDocument: PdfDocumentLike | null = null

    const loadDocument = async () => {
      setIsDocumentLoading(true)
      setDocumentError('')
      setRenderError('')
      setTotalPages(0)

      const canvas = canvasRef.current
      if (canvas) {
        canvas.width = 0
        canvas.height = 0
        canvas.style.width = '0px'
        canvas.style.height = '0px'
      }
      setCurrentPage(Math.max(1, Math.trunc(initialPage)))
      setPageInput(String(Math.max(1, Math.trunc(initialPage))))
      setZoom(clampZoom(initialZoom))

      if (documentRef.current) {
        const previousDocument = documentRef.current
        documentRef.current = null
        void previousDocument.destroy().catch(() => undefined)
      }

      try {
        const pdfjs = await loadPdfJsModule()

        if (!isActive) {
          return
        }

        pdfjs.GlobalWorkerOptions.workerSrc = pdfWorkerSrc

        const loadWithOptions = async (disableWorker: boolean) => {
          const source = {
            data: fileData,
            disableWorker,
            useWorkerFetch: false,
            isEvalSupported: false,
          }

          loadingTask = pdfjs.getDocument(source)
          return loadingTask.promise
        }

        try {
          loadedDocument = await loadWithOptions(false)
        } catch {
          loadedDocument = await loadWithOptions(true)
        }

        if (!isActive || !loadedDocument) {
          if (loadedDocument) {
            void loadedDocument.destroy().catch(() => undefined)
          }
          return
        }

        documentRef.current = loadedDocument
        setTotalPages(loadedDocument.numPages)
        setCurrentPage((previousPage) => Math.min(Math.max(1, previousPage), loadedDocument.numPages))
      } catch (error) {
        setDocumentError(error instanceof Error ? error.message : 'Не удалось загрузить PDF')
      } finally {
        if (isActive) {
          setIsDocumentLoading(false)
        }
      }
    }

    void loadDocument()

    return () => {
      isActive = false
      loadingTask?.destroy?.()

      if (loadedDocument) {
        void loadedDocument.destroy().catch(() => undefined)
      }
    }
  }, [fileData])

  useEffect(() => {
    setPageInput(String(currentPage))
  }, [currentPage])

  useEffect(() => {
    if (!totalPages || isDocumentLoading) {
      return
    }

    onProgressChange?.({
      page: currentPage,
      zoom,
    })
  }, [currentPage, isDocumentLoading, onProgressChange, totalPages, zoom])

  useEffect(() => {
    const pdfDocument = documentRef.current
    const canvas = canvasRef.current

    if (!pdfDocument || !canvas || !containerWidth || documentError || isDocumentLoading || totalPages === 0) {
      return
    }

    let isCancelled = false
    let renderTask: PdfRenderTaskLike | null = null

    const renderPage = async () => {
      setIsPageRendering(true)
      setRenderError('')

      try {
        const page = await pdfDocument.getPage(currentPage)

        if (isCancelled) {
          return
        }

        const baseViewport = page.getViewport({ scale: 1 })
        const horizontalGap = containerWidth <= 640 ? 8 : 32
        const availableWidth = Math.max(
          220,
          Math.min(FITTED_PAGE_MAX_WIDTH, containerWidth - horizontalGap)
        )
        const fittedScale = availableWidth / baseViewport.width
        const cssScale = fittedScale * zoom
        const outputScale = typeof window !== 'undefined' ? window.devicePixelRatio || 1 : 1
        const renderViewport = page.getViewport({ scale: cssScale * outputScale })
        const cssViewport = page.getViewport({ scale: cssScale })

        const context = canvas.getContext('2d', { alpha: false })

        if (!context) {
          throw new Error('Canvas недоступен в этом браузере')
        }

        canvas.width = Math.max(1, Math.floor(renderViewport.width))
        canvas.height = Math.max(1, Math.floor(renderViewport.height))
        canvas.style.width = `${Math.max(1, Math.floor(cssViewport.width))}px`
        canvas.style.height = `${Math.max(1, Math.floor(cssViewport.height))}px`

        renderTask = page.render({
          canvasContext: context,
          viewport: renderViewport,
        })

        await renderTask.promise
      } catch (error) {
        if (isRenderCancelledError(error)) {
          return
        }

        setRenderError(error instanceof Error ? error.message : 'Не удалось отрисовать страницу PDF')
      } finally {
        if (!isCancelled) {
          setIsPageRendering(false)
        }
      }
    }

    void renderPage()

    return () => {
      isCancelled = true
      renderTask?.cancel?.()
    }
  }, [containerWidth, currentPage, documentError, isDocumentLoading, totalPages, zoom])

  const commitPageInput = () => {
    if (!totalPages) {
      setPageInput('1')
      return
    }

    const nextPage = Number(pageInput)

    if (!Number.isFinite(nextPage)) {
      setPageInput(String(currentPage))
      return
    }

    setCurrentPage(Math.min(totalPages, Math.max(1, Math.trunc(nextPage))))
  }

  const goToPreviousPage = () => {
    setCurrentPage((prev) => Math.max(1, prev - 1))
  }

  const goToNextPage = () => {
    setCurrentPage((prev) => Math.min(totalPages, prev + 1))
  }

  const decreaseZoom = () => {
    setZoom((prev) => clampZoom(prev - ZOOM_STEP))
  }

  const increaseZoom = () => {
    setZoom((prev) => clampZoom(prev + ZOOM_STEP))
  }

  const zoomLabel = useMemo(() => `${Math.round(zoom * 100)}%`, [zoom])

  return (
    <div className={`${styles.pdfViewer} ${isDark ? styles.pdfViewerDark : ''}`}>
      <div className={styles.pdfToolbar}>
        <div className={`${styles.pdfToolbarGroup} ${styles.pdfToolbarNavGroup}`}>
          <button
            type="button"
            className={styles.pdfButton}
            onClick={goToPreviousPage}
            disabled={currentPage <= 1 || isDocumentLoading}
          >
            Назад
          </button>

          <div className={styles.pdfPageBox}>
            <span className={`${styles.pdfMetaLabel} ${styles.pdfPageBoxPrefix}`}>Страница</span>
            <input
              className={styles.pdfPageInput}
              inputMode="numeric"
              value={pageInput}
              onChange={(event) => setPageInput(event.target.value.replace(/[^0-9]/g, ''))}
              onBlur={commitPageInput}
              onKeyDown={(event) => {
                if (event.key === 'Enter') {
                  commitPageInput()
                }
              }}
              aria-label="Номер страницы"
            />
            <span className={`${styles.pdfMetaLabel} ${styles.pdfPageBoxTotal}`}>
              из {totalPages || '—'}
            </span>
            <span className={`${styles.pdfMetaLabel} ${styles.pdfPageBoxCompactTotal}`}>
              / {totalPages || '—'}
            </span>
          </div>

          <button
            type="button"
            className={styles.pdfButton}
            onClick={goToNextPage}
            disabled={currentPage >= totalPages || isDocumentLoading || totalPages === 0}
          >
            Вперёд
          </button>
        </div>

        <div className={`${styles.pdfToolbarGroup} ${styles.pdfToolbarZoomGroup}`}>
          <button
            type="button"
            className={styles.pdfButton}
            onClick={decreaseZoom}
            disabled={zoom <= ZOOM_MIN}
            aria-label="Уменьшить масштаб"
          >
            −
          </button>

          <div className={styles.pdfZoomValue}>{zoomLabel}</div>

          <button
            type="button"
            className={styles.pdfButton}
            onClick={increaseZoom}
            disabled={zoom >= ZOOM_MAX}
            aria-label="Увеличить масштаб"
          >
            +
          </button>
        </div>
      </div>

      <div className={styles.pdfCanvasArea} ref={containerRef}>
        <div className={styles.pdfCanvasScroller}>
          <div className={styles.pdfCanvasCard}>
            <canvas ref={canvasRef} className={styles.pdfCanvas} aria-label={fileName || 'PDF'} />
          </div>
        </div>

        {isDocumentLoading ? <div className={styles.pdfOverlay}>Загрузка PDF...</div> : null}
        {!isDocumentLoading && isPageRendering ? (
          <div className={styles.pdfOverlay}>Отрисовка страницы...</div>
        ) : null}
        {documentError ? <div className={styles.pdfOverlayError}>{documentError}</div> : null}
        {!documentError && renderError ? <div className={styles.pdfOverlayError}>{renderError}</div> : null}
      </div>
    </div>
  )
}
